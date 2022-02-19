<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit\Errors;

use PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

/**
 * @covers \PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError::getNormalizedMessage
 */
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
    public function testMessageNormalizationWithoutTokenTranslation($message, $expected)
    {
        $error = new SyntaxError('test.php', $message);
        $this->assertSame($expected, $error->getNormalizedMessage());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataMessageNormalization()
    {
        return array(
            'Strip leading and trailing information - fatal error' => array(
                'message'  => "Fatal error: 'break' not in the 'loop' or 'switch' context in test.php on line 2",
                'expected' => "'break' not in the 'loop' or 'switch' context",
            ),
            'Strip leading and trailing information - parse error' => array(
                'message'  => "Parse error: unexpected 'Foo' (T_STRING) in test.php on line 2",
                'expected' => "Unexpected 'Foo' (T_STRING)", // Also verifies ucfirst() call is being made.
            ),
            'Strip trailing information, not leading - deprecation' => array(
                'message'  => "Deprecated: The (real) cast is deprecated, use (float) instead in test.php on line 2",
                'expected' => "Deprecated: The (real) cast is deprecated, use (float) instead",
            ),
        );
    }

    /**
     * Test retrieving a normalized error message with token translation.
     *
     * @return void
     */
    public function testMessageNormalizationWithTokenTranslation()
    {
        $message  = 'Parse error: unexpected T_FILE, expecting T_STRING in test.php on line 2';
        $expected = 'Unexpected __FILE__ (T_FILE), expecting T_STRING';

        $error = new SyntaxError('test.php', $message);
        $this->assertSame($expected, $error->getNormalizedMessage(true));
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
    public static function dataFilePathHandling()
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
            'File name containing regex delimiter' => array(
                'filePath' => 'test~file.php',
                'fileName' => 'test~file.php',
            ),
            'Full file path, linux slashes' => array(
                'filePath' => 'path/to/subdir/file.php',
                'fileName' => 'file.php',
            ),
            'File path, windows slashes' => array(
                'filePath' => 'path\to\subdir\file.php',
                'fileName' => 'file.php',
            ),
            'Absolute file path, windows slashes' => array(
                'filePath' => 'C:\path\to\subdir\file.php',
                'fileName' => 'C:\path\to\subdir\file.php',
            ),
            'Relative file path, windows slashes' => array(
                'filePath' => '.\subdir\file.php',
                'fileName' => '.\subdir\file.php',
            ),
            'Phar file name' => array(
                'filePath' => 'phar://031.phar.php/a.php',
                'fileName' => 'phar://031.phar.php/a.php',
            ),
        );
    }
}
