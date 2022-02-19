<?php
namespace PHP_Parallel_Lint\PhpParallelLint\Exceptions;

class NotExistsPathException extends ParallelLintException
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
        $this->message = "Path '$path' not found";
    }

    public function getPath()
    {
        return $this->path;
    }
}
