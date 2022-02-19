<?php
namespace PHP_Parallel_Lint\PhpParallelLint\Exceptions;

class NotImplementCallbackException extends ParallelLintException
{
    protected $className;

    public function __construct($className)
    {
        $this->className = $className;
        $this->message = "Class '$className' does not implement SyntaxErrorCallback interface.";
    }

    public function getClassName()
    {
        return $this->className;
    }
}
