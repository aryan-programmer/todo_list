<?php session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

const USER_ID            = "USER_ID";
const USER_EMAIL         = "USER_EMAIL";
const USER_PASSWORD      = "USER_PASSWORD";
const COOKIE_EXPIRY_TIME = 60 * 60 * 24 * 30;

const TASK_PENDING = "Pending";
const TASK_DONE    = "Done";

try {
	$conn = new mysqli("p:127.0.0.1", "root", "", "todo_list");
} catch (mysqli_sql_exception $ex) {
	die("Failed to connect");
}

$uid = null;

function save_user($user_id, $email, $password, $save_cookie = true) {
	global $uid;
	$uid               = $user_id;
	$_SESSION[USER_ID] = $uid;
	if ($save_cookie) {
		setcookie(USER_EMAIL, $email, time() + COOKIE_EXPIRY_TIME);
		setcookie(USER_PASSWORD, $password, time() + COOKIE_EXPIRY_TIME);
	}
}

function restore_user_id(): bool {
	global $uid;
	if (isset($uid) && $uid !== 0) return true;
	if (isset($_SESSION[USER_ID])) {
		$uid = $_SESSION[USER_ID];
		return true;
	} elseif (isset($_COOKIE[USER_EMAIL]) && isset($_COOKIE[USER_PASSWORD])) {
		return sign_in($_COOKIE[USER_EMAIL], $_COOKIE[USER_PASSWORD], false) === true;
	}
	return false;
}

function unset_user_id() {
	global $uid;
	$uid                    = null;
	$_SESSION[USER_ID]      = null;
	$_COOKIE[USER_EMAIL]    = null;
	$_COOKIE[USER_PASSWORD] = null;
	unset($_SESSION[USER_ID]);
	unset($_COOKIE[USER_EMAIL]);
	unset($_COOKIE[USER_PASSWORD]);
	setcookie(USER_EMAIL, "", time() - 1);
	setcookie(USER_PASSWORD, "", time() - 1);
}

function sign_in($email, $password, $save_cookie = true) {
	global $conn;

	try {
		$stmt = $conn->prepare("SELECT `id`, `email`, `password_hash`
FROM `user`
WHERE `email` = ?;");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$res   = $stmt->get_result();
		$assoc = $res->fetch_all(MYSQLI_ASSOC);
		if (count($assoc) < 1) {
			return "Email not used to sign up a user";
		}
		$db_pwd_hash = $assoc[0]["password_hash"];
		if (!password_verify($password, $db_pwd_hash)) {
			return "Invalid password";
		}
		$uid = (int)$assoc[0]["id"];
		save_user($uid, $email, $password, $save_cookie);
		return true;
	} catch (mysqli_sql_exception $ex) {
		return $ex->getMessage();
	}
}

function sign_up($name, $email, $password) {
	global $conn;

	try {
		$password_hash = password_hash($password, PASSWORD_DEFAULT);
		$stmt          = $conn->prepare("INSERT INTO `user`(`name`, `email`, `password_hash`)
VALUES (?, ?, ?);");
		$stmt->bind_param("sss", $name, $email, $password_hash);
		$stmt->execute();
		$uid = $conn->insert_id;
		save_user($uid, $email, $password, true);
		return true;
	} catch (mysqli_sql_exception $ex) {
		return $ex->getMessage();
	}
}

function get_tasks() {
	global $conn, $uid;

	try {
		$stmt = $conn->prepare("SELECT `id`, `user_id`, `description`, `status`
FROM `task`
WHERE `user_id` = ?;");
		$stmt->bind_param("i", $uid);
		$stmt->execute();
		$result = $stmt->get_result();
		$res    = [];
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$res[] = $row;
		}
		return $res;
	} catch (mysqli_sql_exception $ex) {
		pvar_dump($ex);
		return false;
	}
}

function add_task($description) {
	global $conn, $uid;

	try {
		$stmt = $conn->prepare("INSERT INTO `task`(`user_id`, `description`)
VALUES (?, ?);");
		$stmt->bind_param("is", $uid, $description);
		$stmt->execute();
		return true;
	} catch (mysqli_sql_exception $ex) {
		return $ex->getMessage();
	}
}

//function add_task($description) {
//	global $conn, $uid;
//
//	try {
//		$conn->query("INSERT INTO `task`(`user_id`, `description`)
//VALUES ($uid, '$description');");
//		return true;
//	} catch (mysqli_sql_exception $ex) {
//		return $ex->getMessage();
//	}
//}

function set_task_done($id) {
	global $conn, $uid;

	try {
		$stmt = $conn->prepare("UPDATE `task`
SET `status` = 'Done'
WHERE `id` = ?
  AND `user_id` = ?;");
		$stmt->bind_param("ii", $id, $uid);
		$stmt->execute();
		if ($stmt->affected_rows == 0) {
			return "No such task belonging to the current user found";
		} else {
			return true;
		}
	} catch (mysqli_sql_exception $ex) {
		return $ex->getMessage();
	}
}

function delete_task($id) {
	global $conn, $uid;

	try {
		$stmt = $conn->prepare("DELETE FROM `task`
WHERE `id` = ?
  AND `user_id` = ?;");
		$stmt->bind_param("ii", $id, $uid);
		$stmt->execute();
		if ($stmt->affected_rows == 0) {
			return "No such task belonging to the current user found";
		} else {
			return true;
		}
	} catch (mysqli_sql_exception $ex) {
		return $ex->getMessage();
	}
}

