<?php require_once "./php/html_templates.php";

basic_setup(true);
$id = (int)($_GET["id"] ?? "");
if ($id == 0) {
	err_str("Specify a task id");
} else {
	$res = delete_task($id);
	if ($res !== true) err_str($res);
	else msg_str("Deleted task successfully");
}
redirect_page(PageIndex::Home);
