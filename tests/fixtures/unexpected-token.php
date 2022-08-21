<?php

// Pre-PHP 8.0: E_PARSE | Unexpected ';', expecting variable (T_VARIABLE) or '{' or '$' in ...
// PHP 8.0+:    E_PARSE | Unexpected token ";", expecting variable (T_VARIABLE) or "{" or "$" in ...

$myInteger = 1;
echo $;
