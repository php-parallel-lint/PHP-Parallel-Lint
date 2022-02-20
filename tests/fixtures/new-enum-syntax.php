<?php

// Enums became available in PHP 8.1.
// Pre PHP 8.1 error: Unexpected 'Foo' (T_STRING) in ...
// PHP 8.1+ error:    Case FOO of backed enum Foo must have a value in ...

enum Foo: int {
	case FOO;
	case BAR;
}
