<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests;

use PHP_Parallel_Lint\PhpParallelLint\ParallelLint;
use PHP_Parallel_Lint\PhpParallelLint\Process\PhpExecutable;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

class ParallelLintLintTest extends UnitTestCase
{
    public function testSettersAndGetters()
    {
        $phpExecutable = $this->getPhpExecutable();
        $parallelLint = new ParallelLint($phpExecutable, 10);
        $this->assertSame($phpExecutable, $parallelLint->getPhpExecutable());
        $this->assertSame(10, $parallelLint->getParallelJobs());

        $phpExecutable2 = $this->getPhpExecutable();
        $parallelLint->setPhpExecutable($phpExecutable2);
        $this->assertSame($phpExecutable2, $parallelLint->getPhpExecutable());

        $parallelLint->setParallelJobs(33);
        $this->assertSame(33, $parallelLint->getParallelJobs());

        $parallelLint->setShortTagEnabled(true);
        $this->assertTrue($parallelLint->isShortTagEnabled());

        $parallelLint->setAspTagsEnabled(true);
        $this->assertTrue($parallelLint->isAspTagsEnabled());

        $parallelLint->setShortTagEnabled(false);
        $this->assertFalse($parallelLint->isShortTagEnabled());

        $parallelLint->setAspTagsEnabled(false);
        $this->assertFalse($parallelLint->isAspTagsEnabled());
    }

    public function testEmptyArray()
    {
        $parallelLint = new ParallelLint($this->getPhpExecutable());
        $result = $parallelLint->lint(array());

        $this->assertSame(0, $result->getCheckedFilesCount());
        $this->assertSame(0, $result->getFilesWithSyntaxErrorCount());
        $this->assertFalse($result->hasSyntaxError());
        $this->assertSame(0, count($result->getErrors()));
    }

    public function testNotExistsFile()
    {
        $parallelLint = new ParallelLint($this->getPhpExecutable());
        $result = $parallelLint->lint(array('path/for-not-found/'));

        $this->assertSame(0, $result->getCheckedFilesCount());
        $this->assertSame(0, $result->getFilesWithSyntaxErrorCount());
        $this->assertFalse($result->hasSyntaxError());
        $this->assertSame(1, count($result->getErrors()));
    }

    public function testEmptyFile()
    {
        $parallelLint = new ParallelLint($this->getPhpExecutable());
        $result = $parallelLint->lint(array(PL_TESTROOT . '/fixtures/fixture-01/empty-file'));

        $this->assertSame(1, $result->getCheckedFilesCount());
        $this->assertSame(0, $result->getFilesWithSyntaxErrorCount());
        $this->assertFalse($result->hasSyntaxError());
        $this->assertSame(0, count($result->getErrors()));
    }

    public function testValidFile()
    {
        $parallelLint = new ParallelLint($this->getPhpExecutable());
        $result = $parallelLint->lint(array(PL_TESTROOT . '/fixtures/fixture-02/example.php'));

        $this->assertSame(1, $result->getCheckedFilesCount());
        $this->assertSame(0, $result->getFilesWithSyntaxErrorCount());
        $this->assertSame(0, count($result->getErrors()));
    }

    public function testInvalidFile()
    {
        $parallelLint = new ParallelLint($this->getPhpExecutable());
        $result = $parallelLint->lint(array(PL_TESTROOT . '/fixtures/fixture-03/example.php'));

        $this->assertSame(1, $result->getCheckedFilesCount());
        $this->assertSame(1, $result->getFilesWithSyntaxErrorCount());
        $this->assertTrue($result->hasSyntaxError());
        $this->assertSame(1, count($result->getErrors()));
    }

    public function testDeprecated()
    {
        $parallelLint = new ParallelLint($this->getPhpExecutable());
        $result = $parallelLint->lint(array(PL_TESTROOT . '/fixtures/fixture-05/Foo.php'));
        $this->assertSame(1, $result->getCheckedFilesCount());
        $this->assertSame(0, $result->getFilesWithSyntaxErrorCount());
        $this->assertFalse($result->hasSyntaxError());
        $this->assertSame(0, count($result->getErrors()));

        if (PHP_VERSION_ID < 70000 || PHP_VERSION_ID >= 80000) {
            $this->markTestSkipped('Test for php version 7.0-7.4');
        }

        $parallelLint = new ParallelLint($this->getPhpExecutable());
        $parallelLint->setShowDeprecated(true);
        $result = $parallelLint->lint(array(PL_TESTROOT . '/fixtures/fixture-05/Foo.php'));
        $this->assertSame(1, $result->getCheckedFilesCount());
        $this->assertSame(1, $result->getFilesWithSyntaxErrorCount());
        $this->assertTrue($result->hasSyntaxError());
        $this->assertSame(1, count($result->getErrors()));
    }

    public function testValidAndInvalidFiles()
    {
        $parallelLint = new ParallelLint($this->getPhpExecutable());
        $result = $parallelLint->lint(array(
            PL_TESTROOT . '/fixtures/fixture-02/example.php',
            PL_TESTROOT . '/fixtures/fixture-03/example.php',
        ));

        $this->assertSame(2, $result->getCheckedFilesCount());
        $this->assertSame(1, $result->getFilesWithSyntaxErrorCount());
        $this->assertTrue($result->hasSyntaxError());
        $this->assertSame(1, count($result->getErrors()));
    }

    private function getPhpExecutable()
    {
        return PhpExecutable::getPhpExecutable('php');
    }
}
