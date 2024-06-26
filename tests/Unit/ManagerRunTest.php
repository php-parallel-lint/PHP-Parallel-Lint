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
}
