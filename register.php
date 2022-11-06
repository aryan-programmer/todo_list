<?php require_once "./php/html_templates.php";

$name        = "";
$email       = "";
$password    = "";
$re_password = "";

if (isset($_POST["submit"])) {
	$name       = $_POST["name"] ?? "";
	$email      = $_POST["email"] ?? "";
	$password   = $_POST["password"] ?? "";
	$re_password = $_POST["re_password"] ?? "";

	if ($name === "")
		err_str("No name specified");
	if ($email === "" || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
		err_str("Invalid email");
	if ($password === "")
		err_str("No password specified");
	if ($re_password === "")
		err_str("The password wasn't re-entered");
	if ($password !== $re_password)
		err_str("The passwords don't match");

	if(!has_errors()){
		$res = sign_up($name, $email, $password);
		if ($res !== true) {
			err_str($res);
		} else {
			msg_str("Registered successfully.");
			redirect_page(PageIndex::Home);
		}
	}
}

basic_setup(false);
redirect_if_signed_in();
show_html_start_block(PageIndex::Register);
show_messages(); ?>
	<h1>Register onto <?= TITLE_HTML ?></h1>
	<form class="" action="register.php" method="post">
		<?php
		form_input("Name", "name", "name", "text", true, $name);
		form_input("Email", "email", "email", "email", true, $email);
		form_input("Password", "password", "password", "password", true, $password);
		form_input("Confirm Password", "re_password", "re_password", "password", true, $re_password);
		?>
		<button class="btn btn-primary" type="submit" name="submit" value="y">Register</button>
	</form>
<?php
show_html_end_block();
