<?php

/**
 * @testCase
 */

require __DIR__ . '/../vendor/autoload.php';

use JakubOnderka\PhpParallelLint\ErrorFormatter;
use JakubOnderka\PhpParallelLint\GitLabOutput;
use JakubOnderka\PhpParallelLint\IWriter;
use JakubOnderka\PhpParallelLint\Result;
use JakubOnderka\PhpParallelLint\SyntaxError;
use Tester\Assert;

class OutputTest extends Tester\TestCase
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
                    new JakubOnderka\PhpParallelLint\Error('foo/bar.php', "PHP Parse error: syntax error, unexpected ';'")
                )
            ),
            array(
                'errors' => array(
                    new SyntaxError('foo/bar.php', "Parse error: syntax error, unexpected in foo/bar.php on line 52"),
                    new JakubOnderka\PhpParallelLint\Error('foo/bar.php', "PHP Parse error: syntax error, unexpected ';'")
                )
            ),
        );
    }
}

class TestWriter implements IWriter
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

$testCase = new OutputTest;
$testCase->run();
