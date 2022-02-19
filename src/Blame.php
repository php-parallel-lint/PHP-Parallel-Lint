<?php

namespace PHP_Parallel_Lint\PhpParallelLint;

use JsonSerializable;
use ReturnTypeWillChange;

class Blame implements JsonSerializable
{
    public $name;

    public $email;

    /** @var \DateTime */
    public $datetime;

    public $commitHash;

    public $summary;

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array(
            'name' => $this->name,
            'email' => $this->email,
            'datetime' => $this->datetime,
            'commitHash' => $this->commitHash,
            'summary' => $this->summary,
        );
    }
}
