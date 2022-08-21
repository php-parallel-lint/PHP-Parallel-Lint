<?php

// PHP 5.3:     E_PARSE | Unexpected $end in ...
// Pre-PHP 7.3: E_PARSE | Unexpected end of file in ...
// PHP 7.3+:    E_PARSE | Invalid body indentation level (expecting an indentation level of at least 2) in ...

$indendedHeredoc = <<<BAR
  $foo
$foo
  BAR;
