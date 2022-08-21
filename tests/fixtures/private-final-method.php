<?php

// PHP 8.0+: E_COMPILE_WARNING | Private methods cannot be final as they are never overridden by other classes in ...

class Foo {
	final private function bar() {}
}
