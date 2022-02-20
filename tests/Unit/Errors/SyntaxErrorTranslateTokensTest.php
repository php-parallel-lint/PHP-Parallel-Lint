<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit\Errors;

use PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

/**
 * @covers \PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError::translateTokens
 */
class SyntaxErrorTranslateTokensTest extends UnitTestCase
{
    /**
     * Test retrieving a normalized error message with token translations on.
     *
     * @dataProvider dataTranslateTokens
     *
     * @param string $message  The message input to run the test with.
     * @param string $expected The expected method return value.
     *
     * @return void
     */
    public function testTranslateTokens($message, $expected)
    {
        $error = new SyntaxError('test.php', $message);
        $this->assertSame($expected, $error->getNormalizedMessage(true));
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataTranslateTokens()
    {
        return array(
            'No token name in message' => array(
                'message'  => 'Methods with the same name as their class will not be constructors in a future version of PHP',
                'expected' => 'Methods with the same name as their class will not be constructors in a future version of PHP',
            ),
            'Non-existent token name in message (shouldn\'t be possible)' => array(
                'message'  => 'Unexpected T_1H2, expecting T_STRING',
                'expected' => 'Unexpected T_1H2, expecting T_STRING',
            ),
            'Token names in message, but not in translation list' => array(
                // phpcs:disable Generic.Files.LineLength.TooLong
                'message'  => 'Unexpected \'\' (T_ENCAPSED_AND_WHITESPACE), expecting identifier (T_STRING) or variable (T_VARIABLE) or number (T_NUM_STRING)',
                'expected' => 'Unexpected \'\' (T_ENCAPSED_AND_WHITESPACE), expecting identifier (T_STRING) or variable (T_VARIABLE) or number (T_NUM_STRING)',
                // phpcs:enable Generic.Files.LineLength
            ),
            'PHP 5.3-style message with token name without PHP native translation [1]' => array(
                'message'  => 'Unexpected T_FILE, expecting T_STRING',
                'expected' => 'Unexpected __FILE__ (T_FILE), expecting T_STRING',
            ),
            'PHP 5.3-style message with token name without PHP native translation [2]' => array(
                'message'  => 'Unexpected T_INC',
                'expected' => 'Unexpected ++ (T_INC)',
            ),
            'Message with multiple tokens without PHP native translation' => array(
                'message'  => 'Unexpected T_INC, T_IS_IDENTICAL, T_OBJECT_OPERATOR, T_START_HEREDOC',
                'expected' => 'Unexpected ++ (T_INC), === (T_IS_IDENTICAL), -> (T_OBJECT_OPERATOR), <<< (T_START_HEREDOC)',
            ),
            'PHP 5.4-style message with token name with PHP native translation [1] - prevent double translation' => array(
                'message'  => 'Unexpected \'__FILE__\' (T_FILE), expecting T_STRING',
                'expected' => 'Unexpected \'__FILE__\' (T_FILE), expecting T_STRING',
            ),
            'PHP 5.4-style message with token name with PHP native translation [2] - prevent double translation' => array(
                'message'  => 'Unexpected \'++\' (T_INC) in',
                'expected' => 'Unexpected \'++\' (T_INC) in',
            ),
            'Message with multiple tokens with PHP native translation' => array(
                'message'  => 'Unexpected ++ (T_INC), === (T_IS_IDENTICAL), -> (T_OBJECT_OPERATOR), <<< (T_START_HEREDOC)',
                'expected' => 'Unexpected ++ (T_INC), === (T_IS_IDENTICAL), -> (T_OBJECT_OPERATOR), <<< (T_START_HEREDOC)',
            ),
        );
    }
}
