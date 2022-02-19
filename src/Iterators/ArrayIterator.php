<?php
namespace PHP_Parallel_Lint\PhpParallelLint\Iterators;

class ArrayIterator extends \ArrayIterator
{
    public function getNext()
    {
        $this->next();
        return $this->current();
    }
}
