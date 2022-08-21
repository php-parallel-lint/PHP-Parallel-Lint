<?php

// PHP < 7.0: E_ERROR | Cannot access protected property Foo::$bar in ...
// PHP 7.0+:  E_ERROR | Uncaught Error: Cannot access protected property Foo::$bar in ...

class Foo {
	protected $bar = 'bar';
}

$obj = new Foo();
$prop = $obj->bar;
