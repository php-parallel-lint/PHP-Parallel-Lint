<?php
namespace PHP_Parallel_Lint\PhpParallelLint\Writers;

interface WriterInterface
{
    /**
     * @param string $string
     */
    public function write($string);
}
