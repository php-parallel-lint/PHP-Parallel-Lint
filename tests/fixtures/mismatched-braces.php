<?php

// Pre PHP 8.0: E_PARSE | Unexpected ')', expecting ']'
// PHP 8.0+:    E_PARSE | Unclosed '[' does not match ')' in ...

$array = array();
$array[) = 10;
