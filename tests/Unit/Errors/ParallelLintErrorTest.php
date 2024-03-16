<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit\Errors;

use PHP_Parallel_Lint\PhpParallelLint\Errors\ParallelLintError;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

/**
 * @covers \PHP_Parallel_Lint\PhpParallelLint\Errors\ParallelLintError
 */
class ParallelLintErrorTest extends UnitTestCase
{
    /**
     * Test retrieving the error message.
     *
     * @dataProvider dataGetMessage
     *
     * @param mixed  $message  The message input to run the test with.
     * @param string $expected The expected method return value.
     *
     * @return void
     */
    public function testGetMessage($message, $expected)
    {
        $error = new ParallelLintError('test.php', $message);
        $this->assertSame($expected, $error->getMessage());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataGetMessage()
    {
        return array(
            'Message: empty string' => array(
                'message'  => '',
                'expected' => '',
            ),
            'Message: plain text' => array(
                'message'  => 'plain text',
                'expected' => 'plain text',
            ),
            'Message: plain text with leading and trailing whitespace' => array(
                'message'  => '
  plain text
  ',
                'expected' => '
  plain text',
            ),
        );
    }

    /**
     * Test retrieving the file path.
     *
     * @dataProvider dataGetFilePath
     *
     * @param string $filePath The file path input to run the test with.
     *
     * @return void
     */
    public function testGetFilePath($filePath)
    {
        $error = new ParallelLintError($filePath, '');
        $this->assertSame($filePath, $error->getFilePath());
    }

    /**
     * Test retrieving the short file path.
     *
     * @dataProvider dataGetFilePath
     *
     * @param string $filePath      The file path input to run the test with.
     * @param string $expectedShort The expected method return value.
     *
     * @return void
     */
    public function testGetShortFilePath($filePath, $expectedShort)
    {
        $error = new ParallelLintError($filePath, '');
        $this->assertSame($expectedShort, $error->getShortFilePath());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataGetFilePath()
    {
        $cwd = getcwd();

        return array(
            'No path (empty string)' => array(
                'filePath'      => '',
                'expectedShort' => '',
            ),
            'Filename only' => array(
                'filePath'      => 'file.php',
                'expectedShort' => 'file.php',
            ),
            'Plain path, linux slashes' => array(
                'filePath'      => 'path/to/file.php',
                'expectedShort' => 'path/to/file.php',
            ),
            'Plain path, windows slashes' => array(
                'filePath'      => 'path\to\file.php',
                'expectedShort' => 'path\to\file.php',
            ),
            'Path relative to current working directory' => array(
                'filePath'      => $cwd . '/subdir/file.php',
                'expectedShort' => '/subdir/file.php',
            ),
            'Path relative to current working directory with double working dir' => array(
                'filePath'      => $cwd . $cwd . '/subdir/file.php',
                'expectedShort' => $cwd . '/subdir/file.php',
            ),
        );
    }

    /**
     * Test retrieving the error in Json serialized format.
     *
     * @requires PHP 5.4
     *
     * @return void
     */
    public function testJsonSerialize()
    {
        $expected = '{"type":"error","file":"path\/to\/file.php","message":"error message"}';

        $error = new ParallelLintError('path/to/file.php', 'error message');
        $this->assertJsonStringEqualsJsonString($expected, json_encode($error));
    }
}
