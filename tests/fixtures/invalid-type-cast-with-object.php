<?php

// PHP < 8.0: E_NOTICE  | Object of class stdClass could not be converted to int in ...
// PHP 8.0+:  E_WARNING | Object of class stdClass could not be converted to int in ...

$obj = new stdClass();
echo (int) $obj;
