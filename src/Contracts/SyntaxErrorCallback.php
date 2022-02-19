<?php
namespace PHP_Parallel_Lint\PhpParallelLint\Contracts;

use PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError;

interface SyntaxErrorCallback
{
    /**
     * @param SyntaxError $error
     * @return SyntaxError
     */
    public function errorFound(SyntaxError $error);
}
