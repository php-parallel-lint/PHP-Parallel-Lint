<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Writers;

class FileWriter implements WriterInterface
{
    /** @var string */
    protected $logFile;

    /** @var string */
    protected $buffer;

    public function __construct($logFile)
    {
        $this->logFile = $logFile;
    }

    public function write($string)
    {
        $this->buffer .= $string;
    }

    public function __destruct()
    {
        file_put_contents($this->logFile, $this->buffer);
    }
}
