<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Iterators;

use ReturnTypeWillChange;

class FilteredRecursiveDirectoryIterator extends \RecursiveDirectoryIterator
{
    private $excluded = array();

    public function __construct($path, $flags = null, array $excluded = array())
    {
        $this->excluded = $excluded;

        if ($flags === null) {
            $flags = \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO;
        }

        parent::__construct($path, $flags);
    }

    #[ReturnTypeWillChange]
    public function hasChildren($allowLinks = false)
    {
        var_dump($this->getRealPath());
        if (in_array($this->getBasename(), $this->excluded)) {
            return false;
        }

        return parent::hasChildren($allowLinks);
    }
}
