<?php
namespace JakubOnderka\PhpParallelLint;

class NotImplementCallbackException extends Exception
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
