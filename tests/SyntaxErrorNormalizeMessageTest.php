<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests;

use PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError;
use Tester\Assert;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

class SyntaxErrorNormalizeMessageTest extends UnitTestCase
{
    public function testInWordInErrorMessage()
    {
        $message = 'Fatal error: \'break\' not in the \'loop\' or \'switch\' context in test.php on line 2';
        $error = new SyntaxError('test.php', $message);
        Assert::equal('\'break\' not in the \'loop\' or \'switch\' context', $error->getNormalizedMessage());
    }

    public function testInWordInErrorMessageAndInFileName()
    {
        $message = 'Fatal error: \'break\' not in the \'loop\' or \'switch\' context in test in file.php on line 2';
        $error = new SyntaxError('test in file.php', $message);
        Assert::equal('\'break\' not in the \'loop\' or \'switch\' context', $error->getNormalizedMessage());
    }
}
