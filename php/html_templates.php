<?php require_once "db_common.php";
require_once "pages.php";
require_once "html_templates.php";

const ERRORS     = "errors";
const MESSAGES   = "messages";
const TITLE      = "TODO: list";
const TITLE_HTML = "<span class='bg-warning'>TODO</span>: list";
$h_title         = TITLE;
$h_nav_title     = TITLE_HTML;
$h_head          = function () {};
$h_body_end      = function () {};
$h_show_links    = function ($currPageIdx) {
	global $uid;
	foreach (Page::$pages as $idx => $page) {
		if (!$page->shows_on_header) continue;
		if ($page->type !== PageType::Show) {
			if ($page->type === PageType::RequiresUser) {
				if (!isset($uid)) continue;
			} elseif ($page->type === PageType::RequiresSignedOut) {
				if (isset($uid)) continue;
			}
		}
		if ($idx === $currPageIdx) { ?>
			<li class="nav-item">
				<a class="nav-link active fw-bold" aria-current="page" href="#"><?= $page->name ?></a>
			</li>
		<?php } else { ?>
			<li class="nav-item">
				<a class="nav-link fw-normal" href="<?= $page->path ?>"><?= $page->name ?></a>
			</li>
		<?php }
	}
};

function pvar_dump(...$v) {
	echo '<pre style="max-height: 20em; overflow-y: scroll;">';
	var_dump(...$v);
	echo '</pre>';
}

function form_input(
	string $text,
	string $id,
	string $name,
	string $type = "text",
	       $required = false,
	string $default = "") { ?>
	<div class="mb-3 row">
	<label for="<?= $id ?>" class="col-sm-2 col-form-label"><?= $text ?></label>
	<div class="col-sm-10">
		<input
			id="<?= $id ?>" name="<?= $name ?>" type="<?= $type ?>"
			class="form-control" <?= $required ? "required" : "" ?>
			value="<?= $default ?>"
		>
	</div>
	</div><?php
}

// region ...Errors & Messages
function err_str($e) {
	$_SESSION[ERRORS][] = $e;
}

function has_errors(): bool {
	return isset($_SESSION[ERRORS]) && count($_SESSION[ERRORS]) > 0;
}

function msg_str($m) {
	$_SESSION[MESSAGES][] = $m;
}

function show_messages() {
	global $errors, $messages;
	if (isset($_SESSION[ERRORS]) && count($_SESSION[ERRORS]) > 0) { ?>
		<div class="alert alert-danger">
			<h5>Error(s):</h5>
			<?php foreach ($_SESSION[ERRORS] as $error) echo $error, "<br/>"; ?>
		</div>
		<?php
		$_SESSION[ERRORS] = [];
	}
	if (isset($_SESSION[MESSAGES]) && count($_SESSION[MESSAGES]) > 0) { ?>
		<div class="alert alert-info">
			<h5>Message(s):</h5>
			<?php foreach ($_SESSION[MESSAGES] as $message) echo $message, "<br/>"; ?>
		</div>
		<?php
		$_SESSION[MESSAGES] = [];
	}
}

// endregion Errors & Messages

function redirect_page($page) {
	$loc = Page::$pages[$page];
	header("Location: $loc->path");
	die();
}

function redirect_if_signed_in(): void {
	if (isset($_SESSION[USER_ID])) {
		msg_str("Already signed in.");
		redirect_page(PageIndex::Home);
	}
}

function basic_setup(bool $force_user = false) {
	global $errors;
	$r = restore_user_id();
	if (!$r && $force_user) {
		$errors = [];
		err_str("You haven't signed in yet.");
		redirect_page(PageIndex::SignIn);
		die();
	}
}

function show_html_start_block($currPageIdx = PageIndex::None) {
	global $h_title, $h_head, $h_show_links, $h_nav_title;
	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<title><?= $h_title . " - " . Page::$pages[$currPageIdx]->name ?></title>
		<link href="./bootstrap.min.css" type="text/css" rel="stylesheet" />
		<?php $h_head(); ?>
	</head>
	<body>
	<header class="sticky-top">
		<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
			<div class="container-fluid">
				<a class="navbar-brand" href="#"><?= $h_nav_title ?></a>
				<button
					class="navbar-toggler" type="button" data-bs-toggle="collapse"
					data-bs-target="#navbarSupportedContent"
					aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav me-auto mb-2 mb-lg-0">
						<?php $h_show_links($currPageIdx); ?>
					</ul>
				</div>
			</div>
		</nav>
	</header>

	<main class="container mt-2">
<?php }

function show_html_end_block() {
	global $h_body_end; ?>
	</main>
	<script src="./bootstrap.bundle.min.js"></script>
<?php $h_body_end(); ?>
	</body>
	</html>
<?php }
