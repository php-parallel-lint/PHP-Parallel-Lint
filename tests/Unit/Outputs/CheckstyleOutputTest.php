<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit\Outputs;

use PHP_Parallel_Lint\PhpParallelLint\ErrorFormatter;
use PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError;
use PHP_Parallel_Lint\PhpParallelLint\Outputs\CheckstyleOutput;
use PHP_Parallel_Lint\PhpParallelLint\Result;
use PHP_Parallel_Lint\PhpParallelLint\Tests\Helpers\TestWriter;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

class CheckstyleOutputTest extends UnitTestCase
{
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
}

/*
 * NOTES FOR TESTS
 *
 * - Test that the result is valid XML
 * - Test against schema ?
 * - Test that expected nr of files are present
 * - Test that expected nr of errors per file are present
 * - Test that the message is encoded correctly
 * -
 *
 * Test pattern idea:
 * - Have separate test functions which just contain $input and $expected definitions.
 * - Then have a helper method which is called from each test to do the actual testing (and not have huge amounts of duplicate code)
 *
 *
 * Note: as these tests use simpleXML, that should be marked as a required dev extension in composer.json
 *
 * Use Assert::assertXmlStringEqualsXmlString()
 *
 * Some examples which could be used:
 * https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/tests/Console/Report/FixReport/CheckstyleReporterTest.php
 */