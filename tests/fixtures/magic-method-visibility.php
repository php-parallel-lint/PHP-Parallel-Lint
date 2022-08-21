<?php

// PHP < 8.0: E_WARNING | The magic method __set() must have public visibility and cannot be static in ...
// PHP 8.0+:  E_WARNING | The magic method Foo::__set() must have public visibility in ...

class Foo {
	private function __set($name, $value) {}
}
