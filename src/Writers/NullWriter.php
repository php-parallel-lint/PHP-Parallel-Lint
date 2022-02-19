<?php
namespace PHP_Parallel_Lint\PhpParallelLint\Writers;

class NullWriter implements WriterInterface
{
    /**
     * @param string $string
     */
    public function write($string)
    {
    }
}
