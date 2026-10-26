<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit;

use PHP_Parallel_Lint\PhpParallelLint\Manager;
use PHP_Parallel_Lint\PhpParallelLint\Outputs\TextOutput;
use PHP_Parallel_Lint\PhpParallelLint\Settings;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;
use PHP_Parallel_Lint\PhpParallelLint\Writers\NullWriter;

class ManagerRunTest extends UnitTestCase
{
    public function testBadPath()
    {
        $this->expectExceptionPolyfill('\PHP_Parallel_Lint\PhpParallelLint\Exceptions\PathNotFoundException');

        $settings = $this->prepareSettings();
        $settings->paths = array('path/for-not-found/');
        $manager = $this->getManager($settings);
        $manager->run($settings);
    }

    public function testFilesNotFound()
    {
        $this->expectExceptionPolyfill('\PHP_Parallel_Lint\PhpParallelLint\Exceptions\ParallelLintException');
        $this->expectExceptionMessagePolyfill('No file found to check.');

        $settings = $this->prepareSettings();
        $settings->paths = array('tests/fixtures/fixture-01/');
        $manager = $this->getManager($settings);
        $manager->run($settings);
    }

    public function testSuccess()
    {
        $settings = $this->prepareSettings();
        $settings->paths = array('tests/fixtures/fixture-02/');

        $manager = $this->getManager($settings);
        $result = $manager->run($settings);
        $this->assertFalse($result->hasError());
    }

    public function testError()
    {
        $settings = $this->prepareSettings();
        $settings->paths = array('tests/fixtures/fixture-03/');

        $manager = $this->getManager($settings);
        $result = $manager->run($settings);
        $this->assertTrue($result->hasError());
    }

    public function testExcludeRelativeSubdirectory()
    {
        $settings = $this->prepareSettings();
        $settings->paths = array('tests/fixtures/fixture-04/');

        $manager = $this->getManager($settings);
        $result = $manager->run($settings);
        $this->assertTrue($result->hasError());

        $settings->excluded = array('tests/fixtures/fixture-04/dir1/dir2');

        $manager = $this->getManager($settings);
        $result = $manager->run($settings);
        $this->assertFalse($result->hasError());
    }

    public function testExcludeAbsoluteSubdirectory()
    {
        $settings = $this->prepareSettings();
        $cwd = getcwd();
        $settings->paths = array($cwd . '/tests/fixtures/fixture-04/');
        $settings->excluded = array();

        $manager = $this->getManager($settings);
        $result = $manager->run($settings);
        $this->assertTrue($result->hasError());

        $settings->excluded = array($cwd . '/tests/fixtures/fixture-04/dir1/dir2');

        $manager = $this->getManager($settings);
        $result = $manager->run($settings);
        $this->assertFalse($result->hasError());
    }

    /**
     * Note: the `example.php-dist` file contains a parse error.
     * With multi-part extensions being escaped before being used in the RegexIterator,
     * this file will not be included in the scan and the test will pass.
     */
    public function testMultiPartExtensions()
    {
        $settings = $this->prepareSettings();
        $settings->paths = array('tests/fixtures/fixture-06/');

        $settings->extensions = array('php', 'php.dist');

        $manager = $this->getManager($settings);
        $result = $manager->run($settings);
        $this->assertFalse($result->hasError());
    }

    public function testCachePopulatesOnFirstRun()
    {
        $cacheFile = $this->getCacheFilePath();

        $settings = $this->prepareSettings();
        $settings->paths = array('tests/fixtures/fixture-02/');
        $settings->cache = true;
        $settings->cacheFile = $cacheFile;

        $manager = $this->getManager($settings);
        $result = $manager->run($settings);
        $this->assertFalse($result->hasError());
        $this->assertGreaterThan(0, $result->getCheckedFilesCount());
        $this->assertFileExists($cacheFile);
    }

    public function testCacheReusesOnSecondRun()
    {
        $cacheFile = $this->getCacheFilePath();

        $settings = $this->prepareSettings();
        $settings->paths = array('tests/fixtures/fixture-02/');
        $settings->cache = true;
        $settings->cacheFile = $cacheFile;

        // First run: populate cache
        $manager = $this->getManager($settings);
        $firstResult = $manager->run($settings);
        $firstChecked = $firstResult->getCheckedFilesCount();
        $this->assertGreaterThan(0, $firstChecked);

        // Second run: all files served from cache, none actually linted
        $manager = $this->getManager($settings);
        $secondResult = $manager->run($settings);
        $this->assertFalse($secondResult->hasError());
        $this->assertSame(0, $secondResult->getCheckedFilesCount());
        $this->assertSame($firstChecked, $secondResult->getSkippedFilesCount());
    }

    public function testCacheDoesNotCacheErrors()
    {
        $cacheFile = $this->getCacheFilePath();

        $settings = $this->prepareSettings();
        $settings->paths = array('tests/fixtures/fixture-03/');
        $settings->cache = true;
        $settings->cacheFile = $cacheFile;

        // First run: file has syntax error
        $manager = $this->getManager($settings);
        $firstResult = $manager->run($settings);
        $this->assertTrue($firstResult->hasError());

        // Second run: error should still be reported (not cached)
        $manager = $this->getManager($settings);
        $secondResult = $manager->run($settings);
        $this->assertTrue($secondResult->hasError());
        $this->assertSame(
            $firstResult->getFilesWithSyntaxErrorCount(),
            $secondResult->getFilesWithSyntaxErrorCount()
        );
    }

    public function testCacheWithoutFlagDoesNotCreateFile()
    {
        $cacheFile = $this->getCacheFilePath();

        $settings = $this->prepareSettings();
        $settings->paths = array('tests/fixtures/fixture-02/');
        $settings->cache = false;
        $settings->cacheFile = $cacheFile;

        $manager = $this->getManager($settings);
        $manager->run($settings);
        $this->assertFileNotExistsPolyfill($cacheFile);
    }

    /**
     * @param Settings $settings
     * @return Manager
     */
    private function getManager(Settings $settings)
    {
        $manager = new Manager($settings);
        $manager->setOutput(new TextOutput(new NullWriter()));
        return $manager;
    }

    /**
     * @return Settings
     */
    private function prepareSettings()
    {
        $settings = new Settings();
        $settings->phpExecutable = 'php';
        $settings->shortTag = false;
        $settings->aspTags = false;
        $settings->parallelJobs = 10;
        $settings->extensions = array('php', 'phtml', 'php3', 'php4', 'php5');
        $settings->paths = array('FOR-SET');
        $settings->excluded = array();
        $settings->colors = false;

        return $settings;
    }

    /**
     * @return string
     */
    private function getCacheFilePath()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'parallel-lint-test-' . getmypid() . '.json';
    }

    /**
     * Clean up cache files after each test.
     *
     * @after
     */
    public function removeCacheFile()
    {
        $cacheFile = $this->getCacheFilePath();
        if (is_file($cacheFile)) {
            unlink($cacheFile);
        }
    }

    /**
     * PHPUnit polyfill for assertFileDoesNotExist (PHPUnit < 9).
     *
     * @param string $file
     */
    private function assertFileNotExistsPolyfill($file)
    {
        if (method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist($file);
        } else {
            $this->assertFalse(is_file($file), "File $file should not exist");
        }
    }
}
