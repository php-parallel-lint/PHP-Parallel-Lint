<?php
namespace PHP_Parallel_Lint\PhpParallelLint\Outputs;

use PHP_Parallel_Lint\PhpParallelLint\ErrorFormatter;
use PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError;
use PHP_Parallel_Lint\PhpParallelLint\Result;
use PHP_Parallel_Lint\PhpParallelLint\Writers\WriterInterface;

class CheckstyleOutput implements OutputInterface
{
    private $writer;

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
        $this->writer->write('<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
    }

    public function writeResult(Result $result, ErrorFormatter $errorFormatter, $ignoreFails)
    {
        $this->writer->write('<checkstyle>' . PHP_EOL);
        $errors = array();

        foreach ($result->getErrors() as $error) {
            $message = $error->getMessage();
            if ($error instanceof SyntaxError) {
                $line = $error->getLine();
                $source = "Syntax Error";
            } else {
                $line = 1;
                $source = "Linter Error";
            }

            $errors[$error->getShortFilePath()][] = array(
                'message' => $message,
                'line' => $line,
                'source' => $source
            );
        }

        foreach ($errors as $file => $fileErrors) {
            $this->writer->write(sprintf('    <file name="%s">', $file) . PHP_EOL);
            foreach ($fileErrors as $fileError) {
                $this->writer->write(
                    sprintf(
                        '        <error line="%d" severity="ERROR" message="%s" source="%s" />',
                        $fileError['line'],
                        htmlspecialchars($fileError['message'], ENT_COMPAT, 'UTF-8'),
                        $fileError['source']
                    ) .
                    PHP_EOL
                );
            }
            $this->writer->write('    </file>' . PHP_EOL);
        }

        $this->writer->write('</checkstyle>' . PHP_EOL);
    }
}
