<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit\Errors;

use PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

class SyntaxErrorGetNormalizeMessageTest extends UnitTestCase
{
    public function testInWordInErrorMessage()
    {
        $message = 'Fatal error: \'break\' not in the \'loop\' or \'switch\' context in test.php on line 2';
        $error = new SyntaxError('test.php', $message);
        $this->assertSame('\'break\' not in the \'loop\' or \'switch\' context', $error->getNormalizedMessage());
    }

    public function testInWordInErrorMessageAndInFileName()
    {
        $message = 'Fatal error: \'break\' not in the \'loop\' or \'switch\' context in test in file.php on line 2';
        $error = new SyntaxError('test in file.php', $message);
        $this->assertSame('\'break\' not in the \'loop\' or \'switch\' context', $error->getNormalizedMessage());
    }
}
