<?php
namespace JakubOnderka\PhpParallelLint;

class ArrayIterator extends \ArrayIterator
{
    public function getNext()
    {
        $this->next();
        return $this->current();
    }
}
