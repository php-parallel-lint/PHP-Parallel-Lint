<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit\Errors;

use PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

class SyntaxErrorGetNormalizeMessageTest extends UnitTestCase
{
    const FILEPATH_MSG_TEMPLATE = "Parse error: unexpected 'Foo' (T_STRING) in %s on line 2";
    const FILEPATH_MSG_EXPECTED = "Unexpected 'Foo' (T_STRING)";

    /**
     * Test retrieving a normalized error message.
     *
     * @dataProvider dataMessageNormalization
     *
     * @param string $message  The message input to run the test with.
     * @param string $expected The expected method return value.
     *
     * @return void
     */
    public function testMessageNormalization($message, $expected)
    {
        $error = new SyntaxError('test.php', $message);
        $this->assertSame($expected, $error->getNormalizedMessage());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataMessageNormalization()
    {
        return array(
            'Strip leading and trailing information' => array(
                'message'  => "Fatal error: 'break' not in the 'loop' or 'switch' context in test.php on line 2",
                'expected' => "'break' not in the 'loop' or 'switch' context",
            ),
        );
    }

    /**
     * Test retrieving a normalized error message with variations for the file path.
     *
     * @dataProvider dataFilePathHandling
     *
     * @param string $filePath The file path input to run the test with.
     * @param string $fileName The file name which is expected to be in the error message.
     *
     * @return void
     */
    public function testFilePathHandling($filePath, $fileName)
    {
        $message = sprintf(self::FILEPATH_MSG_TEMPLATE, $fileName);
        $error   = new SyntaxError($filePath, $message);
        $this->assertSame(self::FILEPATH_MSG_EXPECTED, $error->getNormalizedMessage());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataFilePathHandling()
    {
        return array(
            'Plain file name' => array(
                'filePath' => 'test.php',
                'fileName' => 'test.php',
            ),
            'File name containing spaces' => array(
                'filePath' => 'test in file.php',
                'fileName' => 'test in file.php',
            ),
        );
    }
}
