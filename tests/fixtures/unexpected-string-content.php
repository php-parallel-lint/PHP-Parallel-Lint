<?php

// Pre PHP 8.0: E_PARSE | Unexpected '' (T_ENCAPSED_AND_WHITESPACE), expecting identifier (T_STRING) or variable (T_VARIABLE) or number (T_NUM_STRING) in ...
// PHP 8.0+:    E_PARSE | Unexpected string content "", expecting "-" or identifier or variable or number

$arr = array('foo' => 'bar');
echo "$arr['foo']";
