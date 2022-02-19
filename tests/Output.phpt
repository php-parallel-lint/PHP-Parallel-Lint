<?php

/**
 * @testCase
 */

require_once __DIR__ . '/../src/polyfill.php';
require __DIR__ . '/../vendor/autoload.php';

use PHP_Parallel_Lint\PhpParallelLint\ErrorFormatter;
use PHP_Parallel_Lint\PhpParallelLint\Errors\ParallelLintError;
use PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError;
use PHP_Parallel_Lint\PhpParallelLint\Outputs\CheckstyleOutput;
use PHP_Parallel_Lint\PhpParallelLint\Outputs\GitLabOutput;
use PHP_Parallel_Lint\PhpParallelLint\Result;
use PHP_Parallel_Lint\PhpParallelLint\Writers\WriterInterface;
use Tester\Assert;
use Tester\TestCase;

class OutputTest extends TestCase
{
    /**
     * @dataProvider getGitLabOutputData
     */
    public function testGitLabOutput($errors)
    {
        $result = new Result($errors, array(), array(), 0);
        $writer = new TestWriter();
        $output = new GitLabOutput($writer);

        $output->writeResult($result, new ErrorFormatter(), true);

        $result = (array) json_decode($writer->getLogs());

        for ($i = 0; $i < count($result) && $i < count($errors); $i++) {
            $message = $errors[$i]->getMessage();
            $filePath = $errors[$i]->getFilePath();
            $line = 1;
            if ($errors[$i] instanceof SyntaxError) {
                $line = $errors[$i]->getLine();
            }
            Assert::equal($result[$i]->type, 'issue');
            Assert::equal($result[$i]->check_name, 'Parse error');
            Assert::equal($result[$i]->categories, 'Style');
            Assert::equal($result[$i]->severity, 'minor');
            Assert::equal($result[$i]->description, $message);
            Assert::equal($result[$i]->fingerprint, md5($filePath . $message . $line));
            Assert::equal($result[$i]->location->path, $filePath);
            Assert::equal($result[$i]->location->lines->begin, $line);
        }
    }

    public function testCheckstyleOutput()
    {
        $errors = array(
            new SyntaxError(
                'sample.php',
                'Parse error: syntax error, unexpected \'"\' in ./sample.php on line 3'
            ),
        );

        $result = new Result($errors, array(), array(), 0);
        $writer = new TestWriter();
        $output = new CheckstyleOutput($writer);

        $output->writeResult($result, new ErrorFormatter(), true);
        $xml = $writer->getLogs();
        // phpcs:ignore Generic.PHP.NoSilencedErrors -- Test only code, this is okay.
        $parsed = @simplexml_load_string($xml);

        Assert::contains("unexpected '&quot;'", $xml);
        Assert::type('SimpleXMLElement', $parsed);
    }

    public function getGitLabOutputData()
    {
        return array(
            array(
                'errors' => array()
            ),
            array(
                'errors' => array(
                    new SyntaxError('foo/bar.php', "Parse error: syntax error, unexpected in foo/bar.php on line 52")
                )
            ),
            array(
                'errors' => array(
                    new ParallelLintError('foo/bar.php', "PHP Parse error: syntax error, unexpected ';'")
                )
            ),
            array(
                'errors' => array(
                    new SyntaxError('foo/bar.php', "Parse error: syntax error, unexpected in foo/bar.php on line 52"),
                    new ParallelLintError('foo/bar.php', "PHP Parse error: syntax error, unexpected ';'")
                )
            ),
        );
    }
}

class TestWriter implements WriterInterface
{
    /** @var string */
    protected $logs = "";

    /**
     * @param string $string
     */
    public function write($string)
    {
        $this->logs .= $string;
    }

    public function getLogs()
    {
        return $this->logs;
    }
}

$testCase = new OutputTest();
$testCase->run();
