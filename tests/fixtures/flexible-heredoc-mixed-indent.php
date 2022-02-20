<?php

// Pre-PHP 7.3: Unexpected end of file in ...
// PHP 7.3+:    Invalid indentation - tabs and spaces cannot be mixed in ...

$indendedHeredoc = <<<BAR
        $foo
		$foo
        $foo
		BAR;
