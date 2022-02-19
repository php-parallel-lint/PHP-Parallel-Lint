<?php
namespace PHP_Parallel_Lint\PhpParallelLint\Writers;

class ConsoleWriter implements WriterInterface
{
    /**
     * @param string $string
     */
    public function write($string)
    {
        echo $string;
    }
}
