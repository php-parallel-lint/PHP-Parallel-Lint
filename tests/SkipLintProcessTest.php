<?php

/**
 * @testCase
 */

namespace PHP_Parallel_Lint\PhpParallelLint\Tests;

require_once __DIR__ . '/../src/polyfill.php';
require __DIR__ . '/../vendor/autoload.php';

use PHP_Parallel_Lint\PhpParallelLint\Process\PhpExecutable;
use PHP_Parallel_Lint\PhpParallelLint\Process\SkipLintProcess;
use Tester\Assert;
use Tester\TestCase;

class SkipLintProcessTest extends TestCase
{
    public function testLargeInput()
    {
        $filesToCheck = array(
            __DIR__ . '/skip-on-5.3/class.php',
            __DIR__ . '/skip-on-5.3/trait.php',
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
            Assert::notEqual(null, $status);
        }
    }
}

$skipLintProcessTest = new SkipLintProcessTest();
$skipLintProcessTest->run();
