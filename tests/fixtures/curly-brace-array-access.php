<?php

// Deprecated in PHP 7.4, removed in PHP 8.0.
// PHP 7.4:  E_DEPRECATED    | Deprecated: Array and string offset access syntax with curly braces is deprecated in ...
// PHP 8.0+: E_COMPILE_ERROR | Array and string offset access syntax with curly braces is no longer supported in ...

$text = 'text';
echo $text{2};
