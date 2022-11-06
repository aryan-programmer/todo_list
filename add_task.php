<?php require_once "./php/html_templates.php";

basic_setup(true);
$description = $_POST["description"] ?? "";
if (strlen($description) == 0) {
	err_str("Enter a task description");
} else {
	$res = add_task($description);
	if ($res !== true) err_str($res);
}
redirect_page(PageIndex::Home);
