<?php require_once "./php/html_templates.php";

$email    = "";
$password = "";

if (isset($_POST["submit"])) {
	$email    = $_POST["email"] ?? "";
	$password = $_POST["password"] ?? "";

	if ($email === "" || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
		err_str("Invalid email");
	if ($password === "")
		err_str("No password specified");

	if (!has_errors()) {
		$res = sign_in($email, $password);
		if ($res !== true) {
			err_str($res);
		} else {
			msg_str("Signed in successfully.");
			redirect_page(PageIndex::Home);
		}
	}
}

basic_setup(false);
redirect_if_signed_in();
show_html_start_block(PageIndex::SignIn);
show_messages(); ?>
	<h1>Sign in to <?= TITLE_HTML ?></h1>
	<form class="" action="sign_in.php" method="post">
		<?php
		form_input("Email", "email", "email", "email", true, $email);
		form_input("Password", "password", "password", "password", true, $password);
		?>
		<button class="btn btn-primary" type="submit" name="submit" value="y">Sign in</button>
	</form>
<?php
show_html_end_block();
