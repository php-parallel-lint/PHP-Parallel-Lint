<?php
namespace PhpParallelLint\PhpParallelLint\Iterators;

class ArrayIterator extends \ArrayIterator
{
    public function getNext()
    {
        $this->next();
        return $this->current();
    }
}
