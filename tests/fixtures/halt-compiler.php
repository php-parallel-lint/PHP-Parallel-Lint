<?php

// PHP 5.3-5.6: E_COMPILE_ERROR | __HALT_COMPILER() can only be used from the outermost scope in ...
// PHP 7.0-7.4: E_PARSE         | Unexpected end of file in ...
// PHP 8.0+:    E_PARSE         | Unclosed '{' on line 2 in ...

namespace foo {
    __halt_compiler();

?>
