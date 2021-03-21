<?php
namespace PhpParallelLint\PhpParallelLint\Contracts;

use PhpParallelLint\PhpParallelLint\Errors\SyntaxError;

interface SyntaxErrorCallback
{
    /**
     * @param SyntaxError $error
     * @return SyntaxError
     */
    public function errorFound(SyntaxError $error);
}
