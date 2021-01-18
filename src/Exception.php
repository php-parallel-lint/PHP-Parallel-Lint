<?php
namespace JakubOnderka\PhpParallelLint;

use ReturnTypeWillChange;

class Exception extends \Exception implements \JsonSerializable
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
