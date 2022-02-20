<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Outputs;

use PHP_Parallel_Lint\PhpParallelLint\ErrorFormatter;
use PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError;
use PHP_Parallel_Lint\PhpParallelLint\Result;
use PHP_Parallel_Lint\PhpParallelLint\Writers\WriterInterface;

class CheckstyleOutput implements OutputInterface
{
/*
 * TODO NOTES based on code review and compare with PHPCS & CS-Fixer:
 * - Severity PR: https://github.com/php-parallel-lint/PHP-Parallel-Lint/pull/68/files
 * - Encoding of all text should be UTF-8 - at this moment there is no guarantee it is.
 * - Should use XMLWriter (enabled by default), SimpleXML (enabled by default) or DOM (enabled by default) to actually create the XML, not manual.
 *   => Note: would add new extension dependency to the application!
 * - Add Application::VERSION to the open tag ?
 * - While message is now escaped for special chars, nothing else is, so there may be more issues - file with quote in name ?
 *   Also: escaping should use the ENT_XML1 flag if available:
 *   htmlspecialchars($string, ENT_XML1 | ENT_COMPAT, 'UTF-8');
 *   Probably should also use `ENT_SUBSTITUTE`?
 * - Should messages include file name + line nr (as they do now) or should those be stripped as line and file are already available ?
 *
 * Helpful:
 * Specs: https://checkstyle.sourceforge.io/property_types.html#SeverityLevel
 * XSD: https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/v3.0.2/doc/schemas/fix/checkstyle.xsd
 * XSD: https://github.com/linkedin/pygradle/blob/master/pygradle-plugin/src/test/resources/checkstyle/checkstyle.xsd
 * CS-Fixer report: https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/src/Console/Report/FixReport/CheckstyleReporter.php
 * PHPStan report: https://github.com/phpstan/phpstan-src/blob/master/src/Command/ErrorFormatter/CheckstyleErrorFormatter.php
 * Psalm report: https://github.com/vimeo/psalm/blob/4.x/src/Psalm/Report/CheckstyleReport.php
 *
 * Related issue:
 * - https://github.com/checkstyle/checkstyle/issues/5166 (request for XSD)
 */
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
