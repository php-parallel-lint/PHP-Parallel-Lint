<?php
namespace PhpParallelLint\PhpParallelLint\Writers;

interface WriterInterface
{
    /**
     * @param string $string
     */
    public function write($string);
}
