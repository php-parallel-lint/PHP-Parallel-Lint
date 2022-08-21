<?php

// E_COMPILE_ERROR | Cannot redefine class constant Foo::BAR in ...

class Foo {
	const BAR = 1;
	const BAR = 2;
}
