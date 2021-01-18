<?php
namespace JakubOnderka\PhpParallelLint;

class NotExistsClassException extends Exception
{
    protected $className;
    protected $fileName;

    public function __construct($className, $fileName)
    {
        $this->className = $className;
        $this->fileName = $fileName;
        $this->message = "Class with name '$className' does not exists in file '$fileName'";
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getFileName()
    {
        return $this->fileName;
    }
}
