<?php
namespace PhpParallelLint\PhpParallelLint\Exceptions;

use ReturnTypeWillChange;

class ParallelLintException extends \Exception implements \JsonSerializable
{
    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array(
            'type' => get_class($this),
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
        );
    }
}
