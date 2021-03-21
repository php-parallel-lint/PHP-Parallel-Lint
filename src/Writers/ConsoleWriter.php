<?php
namespace PhpParallelLint\PhpParallelLint\Writers;

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
