<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit\Errors;

use PHP_Parallel_Lint\PhpParallelLint\Blame;
use PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

class SyntaxErrorOtherTest extends UnitTestCase
{
    /**
     * Test setting and getting the blame.
     *
     * @covers \PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError::setBlame
     * @covers \PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError::getBlame
     *
     * @return void
     */
    public function testGetSetBlame()
    {
        $blame = new Blame();
        $error = new SyntaxError('test.php', 'message');

        $error->setBlame($blame);
        $this->assertSame($blame, $error->getBlame());
    }

    /**
     * Test retrieving the error in Json serialized format.
     *
     * @covers \PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError::jsonSerialize
     *
     * @requires PHP 5.4
     *
     * @return void
     */
    public function testJsonSerialize()
    {
        // phpcs:ignore Generic.Files.LineLength.MaxExceeded
        $expected = '{"type":"syntaxError","file":"path\/to\/file.php","line":2,"message":"Parse error: unexpected \'Foo\' (T_STRING) in file.php on line 2","normalizeMessage":"Unexpected \'Foo\' (T_STRING)","blame":null}';

        $error = new SyntaxError('path/to/file.php', "Parse error: unexpected 'Foo' (T_STRING) in file.php on line 2");
        $this->assertJsonStringEqualsJsonString($expected, json_encode($error));
    }
}
