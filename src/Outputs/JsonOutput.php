<?php
namespace PhpParallelLint\PhpParallelLint\Outputs;

use PhpParallelLint\PhpParallelLint\ErrorFormatter;
use PhpParallelLint\PhpParallelLint\Result;
use PhpParallelLint\PhpParallelLint\Writers\WriterInterface;

class JsonOutput implements OutputInterface
{
    /** @var WriterInterface */
    protected $writer;

    /** @var int */
    protected $phpVersion;

    /** @var int */
    protected $parallelJobs;

    /** @var string */
    protected $hhvmVersion;

    /**
     * @param WriterInterface $writer
     */
    public function __construct(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    public function ok()
    {

    }

    public function skip()
    {

    }

    public function error()
    {

    }

    public function fail()
    {

    }

    public function setTotalFileCount($count)
    {

    }

    public function writeHeader($phpVersion, $parallelJobs, $hhvmVersion = null)
    {
        $this->phpVersion = $phpVersion;
        $this->parallelJobs = $parallelJobs;
        $this->hhvmVersion = $hhvmVersion;
    }

    public function writeResult(Result $result, ErrorFormatter $errorFormatter, $ignoreFails)
    {
        echo json_encode(array(
            'phpVersion' => $this->phpVersion,
            'hhvmVersion' => $this->hhvmVersion,
            'parallelJobs' => $this->parallelJobs,
            'results' => $result,
        ));
    }
}
