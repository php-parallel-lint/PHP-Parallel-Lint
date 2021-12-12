<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Exceptions;

class InvalidArgumentException extends ParallelLintException
{
    protected $argument;

    public function __construct($argument)
    {
        $this->argument = $argument;
        $this->message = "Invalid argument $argument";
    }

    public function getArgument()
    {
        return $this->argument;
    }
}
