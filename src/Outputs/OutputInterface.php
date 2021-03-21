<?php
namespace PhpParallelLint\PhpParallelLint\Outputs;

use PhpParallelLint\PhpParallelLint\ErrorFormatter;
use PhpParallelLint\PhpParallelLint\Result;
use PhpParallelLint\PhpParallelLint\Writers\WriterInterface;

interface OutputInterface
{
    public function __construct(WriterInterface $writer);

    public function ok();

    public function skip();

    public function error();

    public function fail();

    public function setTotalFileCount($count);

    public function writeHeader($phpVersion, $parallelJobs, $hhvmVersion = null);

    public function writeResult(Result $result, ErrorFormatter $errorFormatter, $ignoreFails);
}
