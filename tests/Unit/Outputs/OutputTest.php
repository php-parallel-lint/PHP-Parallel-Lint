<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit\Outputs;

use PHP_Parallel_Lint\PhpParallelLint\ErrorFormatter;
use PHP_Parallel_Lint\PhpParallelLint\Errors\ParallelLintError;
use PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError;
use PHP_Parallel_Lint\PhpParallelLint\Outputs\CheckstyleOutput;
use PHP_Parallel_Lint\PhpParallelLint\Outputs\GitLabOutput;
use PHP_Parallel_Lint\PhpParallelLint\Result;
use PHP_Parallel_Lint\PhpParallelLint\Tests\Helpers\TestWriter;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

class OutputTest extends UnitTestCase
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

        $this->assertSame(count($errors), count($result));

        for ($i = 0; $i < count($result) && $i < count($errors); $i++) {
            $message = $errors[$i]->getMessage();
            $filePath = $errors[$i]->getFilePath();
            $line = 1;
            if ($errors[$i] instanceof SyntaxError) {
                $line = $errors[$i]->getLine();
            }
            $this->assertSame($result[$i]->type, 'issue');
            $this->assertSame($result[$i]->check_name, 'Parse error');
            $this->assertSame($result[$i]->categories, 'Style');
            $this->assertSame($result[$i]->severity, 'minor');
            $this->assertSame($result[$i]->description, $message);
            $this->assertSame($result[$i]->fingerprint, md5($filePath . $message . $line));
            $this->assertSame($result[$i]->location->path, $filePath);
            $this->assertSame($result[$i]->location->lines->begin, $line);
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

        $this->assertStringContainsStringPolyfill("unexpected '&quot;'", $xml);
        $this->assertInstanceOf('SimpleXMLElement', $parsed);
    }

    public static function getGitLabOutputData()
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
