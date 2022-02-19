<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Iterators;

use ArrayIterator as PHPArrayIterator;

class ArrayIterator extends PHPArrayIterator
{
    public function getNext()
    {
        $this->next();
        return $this->current();
    }
}
