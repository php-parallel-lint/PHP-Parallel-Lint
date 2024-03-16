<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit\Errors;

use PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

/**
 * @covers \PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError::getLine
 */
class SyntaxErrorGetLineTest extends UnitTestCase
{
    /**
     * Test retrieving the line on which the error occured.
     *
     * @dataProvider dataGetLine
     *
     * @param string $message  The message input to run the test with.
     * @param string $expected The expected method return value.
     *
     * @return void
     */
    public function testGetLine($message, $expected)
    {
        $error = new SyntaxError('test.php', $message);
        $this->assertSame($expected, $error->getLine());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataGetLine()
    {
        return array(
            'Message: empty string' => array(
                'message'  => '',
                'expected' => null,
            ),
            'Message: plain text, no line number' => array(
                'message'  => 'plain text',
                'expected' => null,
            ),
            'Message: error with line number at end' => array(
                'message'  => 'Parse error: syntax error, unexpected token "<", expecting end of file in Source string on line 10',
                'expected' => 10,
            ),
            'Message: error with line number at end with trailing whitespace' => array(
                'message'  => 'Parse error: syntax error, unexpected token "<", expecting end of file in Source string on line 10   ',
                'expected' => 10,
            ),
            'Message: error with line number at end and in the message text [1]' => array(
                'message'  => 'Parse error: Unclosed \'{\' on line 2 in test.php on line 5',
                'expected' => 5,
            ),
            'Message: error with line number at end and in the message text [2]' => array(
                'message'  => 'Parse error: Unterminated comment starting on line 2 in test.php on line 3',
                'expected' => 3,
            ),
            'Message: error with line number at end, large number' => array(
                'message'  => 'The (real) cast has been removed, use (float) instead in test.php on line 3534384',
                'expected' => 3534384,
            ),
        );
    }
}
