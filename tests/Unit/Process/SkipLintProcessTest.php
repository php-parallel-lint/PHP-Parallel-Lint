<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit\Process;

use PHP_Parallel_Lint\PhpParallelLint\Process\PhpExecutable;
use PHP_Parallel_Lint\PhpParallelLint\Process\SkipLintProcess;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

class SkipLintProcessTest extends UnitTestCase
{
    public function testLargeInput()
    {
        $filesToCheck = array(
            PL_TESTROOT . '/fixtures/skip-on-5.3/class.php',
            PL_TESTROOT . '/fixtures/skip-on-5.3/trait.php',
        );

        for ($i = 0; $i < 15; $i++) {
            $filesToCheck = array_merge($filesToCheck, $filesToCheck);
        }

        $phpExecutable = PhpExecutable::getPhpExecutable('php');
        $process = new SkipLintProcess($phpExecutable, $filesToCheck);

        while (!$process->isFinished()) {
            usleep(100);
            $process->getChunk();
        }

        foreach ($filesToCheck as $fileToCheck) {
            $status = $process->isSkipped($fileToCheck);
            $this->assertNotNull($status);
        }
    }
}
