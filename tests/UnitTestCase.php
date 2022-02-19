<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests;

use PHPUnit\Framework\TestCase;

abstract class UnitTestCase extends TestCase
{
    /**
     * @var string
     */
    private $exceptionMessage = '';

    /**
     * Clear any stored exception information between each test.
     *
     * @after
     */
    public function clearExceptionInfo()
    {
        $this->exceptionMessage = '';
    }

    /**
     * PHPUnit Polyfill: Set an expectation to receive a particular type of Exception.
     *
     * @param mixed $exception The name of the exception to expect.
     *
     * @return void
     */
    public function expectExceptionPolyfill($exception)
    {
        if (method_exists('\PHPUnit\Framework\TestCase', 'expectException')) {
            // PHPUnit >= 5.2.0.
            parent::expectException($exception);
            return;
        }

        $this->setExpectedException($exception, $this->exceptionMessage);
    }

    /**
     * PHPUnit Polyfill: Set an expectation to receive an Exception with a particular error message.
     *
     * @param string $message The error message to expect.
     *
     * @return void
     */
    public function expectExceptionMessagePolyfill($message)
    {
        if (method_exists('\PHPUnit\Framework\TestCase', 'expectExceptionMessage')) {
            // PHPUnit >= 5.2.0.
            parent::expectExceptionMessage($message);
            return;
        }

        // Store the received message in case any of the other methods are called as well.
        $this->exceptionMessage = $message;

        $exception = $this->getExpectedException();
        $this->setExpectedException($exception, $message);
    }

    /**
     * PHPUnit Polyfill: Asserts that a string haystack contains a needle.
     *
     * @param string $needle   The string to search for.
     * @param string $haystack The string to treat as the haystack.
     * @param string $message  Optional failure message to display.
     *
     * @return void
     */
    public static function assertStringContainsStringPolyfill($needle, $haystack, $message = '')
    {
        if (\method_exists('\PHPUnit\Framework\Assert', 'assertStringContainsString')) {
            // PHPUnit >= 7.5.0.
            parent::assertStringContainsString($needle, $haystack, $message);
            return;
        }

        // PHPUnit < 7.5.0.
        if ($needle === '') {
            static::assertSame($needle, $needle, $message);
            return;
        }

        static::assertContains($needle, $haystack, $message);
    }
}
