<?php
namespace PhpParallelLint\PhpParallelLint\Outputs;

use PhpParallelLint\PhpParallelLint\Outputs\OutputInterface;
use PhpParallelLint\PhpParallelLint\ErrorFormatter;
use PhpParallelLint\PhpParallelLint\Errors\SyntaxError;
use PhpParallelLint\PhpParallelLint\Result;
use PhpParallelLint\PhpParallelLint\Writers\WriterInterface;

class GitLabOutput implements OutputInterface
{
    /** @var WriterInterface */
    protected $writer;

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
    }

    public function writeResult(Result $result, ErrorFormatter $errorFormatter, $ignoreFails)
    {
        $errors = array();
        foreach ($result->getErrors() as $error) {
            $message = $error->getMessage();
            $line = 1;
            if ($error instanceof SyntaxError) {
                $line = $error->getLine();
            }
            $filePath = $error->getFilePath();
            $result = array(
                'type' => 'issue',
                'check_name' => 'Parse error',
                'description' => $message,
                'categories' => 'Style',
                'fingerprint' => md5($filePath . $message . $line),
                'severity' => 'minor',
                'location' => array(
                    'path' => $filePath,
                    'lines' => array(
                        'begin' => $line,
                    ),
                ),
            );
            array_push($errors, $result);
        }

        $string = json_encode($errors) . PHP_EOL;
        $this->writer->write($string);
    }
}
