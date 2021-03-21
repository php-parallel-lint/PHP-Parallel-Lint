<?php
namespace PhpParallelLint\PhpParallelLint\Writers;

class NullWriter implements WriterInterface
{
    /**
     * @param string $string
     */
    public function write($string)
    {
    }
}
