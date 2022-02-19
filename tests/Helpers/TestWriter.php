<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Helpers;

use PHP_Parallel_Lint\PhpParallelLint\Writers\WriterInterface;

class TestWriter implements WriterInterface
{
    /** @var string */
    protected $logs = "";

    /**
     * @param string $string
     */
    public function write($string)
    {
        $this->logs .= $string;
    }

    public function getLogs()
    {
        return $this->logs;
    }
}
