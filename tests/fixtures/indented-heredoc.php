<?php

// Pre-PHP 7.3: Unexpected end of file in ...
// PHP 7.3+:    Invalid body indentation level (expecting an indentation level of at least 2) in ...

$indendedHeredoc = <<<BAR
  $foo
$foo
  BAR;
