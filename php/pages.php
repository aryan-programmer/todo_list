<?php

abstract class PageType {
	const Show              = 0;
	const RequiresUser      = self::Show + 1;
	const RequiresSignedOut = self::RequiresUser + 1;
}

abstract class PageIndex {
	const None     = -1;
	const Home     = self::None + 1;
	const Register = self::Home + 1;
	const SignIn   = self::Register + 1;
	const SignOut  = self::SignIn + 1;
}

class Page {
	/** @var Page[] $pages */
	static $pages;
	public $path;
	public $name;
	public $shows_on_header;
	public $type;

	public function __construct($path, $name, $shows_on_header, $type = PageType::Show) {
		$this->path            = (string)$path;
		$this->name            = (string)$name;
		$this->shows_on_header = $shows_on_header === true;
		$this->type            = (int)$type;
	}
}

Page::$pages = [
	PageIndex::None     => new Page("index.php", "Page", false),
	PageIndex::Home     => new Page("index.php", "Home", true),
	PageIndex::Register => new Page("register.php", "Register", true, PageType::RequiresSignedOut),
	PageIndex::SignIn   => new Page("sign_in.php", "Sign In", true, PageType::RequiresSignedOut),
	PageIndex::SignOut  => new Page("sign_out.php", "Sign Out", true, PageType::RequiresUser),
];
