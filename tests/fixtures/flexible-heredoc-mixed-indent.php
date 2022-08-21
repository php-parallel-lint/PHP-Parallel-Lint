<?php

// Pre-PHP 7.3: E_PARSE | Unexpected end of file in ...
// PHP 7.3+:    E_PARSE | Invalid indentation - tabs and spaces cannot be mixed in ...

$indendedHeredoc = <<<BAR
        $foo
		$foo
        $foo
		BAR;
