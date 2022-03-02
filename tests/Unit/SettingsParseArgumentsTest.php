<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit;

use PHP_Parallel_Lint\PhpParallelLint\Settings;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

class SettingsParseArgumentsTest extends UnitTestCase
{
    public function testNoneArguments()
    {
        $commandLine = "./parallel-lint .";
        $argv = explode(" ", $commandLine);
        $settings = Settings::parseArguments($argv);

        $expectedSettings = new Settings();
        $expectedSettings->shortTag = false;
        $expectedSettings->aspTags = false;
        $expectedSettings->parallelJobs = 10;
        $expectedSettings->extensions = array('php', 'phtml', 'php3', 'php4', 'php5', 'phpt');
        $expectedSettings->paths = array('.');
        $expectedSettings->excluded = array();
        $expectedSettings->colors = Settings::AUTODETECT;
        $expectedSettings->showProgress = true;
        $expectedSettings->format = Settings::FORMAT_TEXT;
        $expectedSettings->syntaxErrorCallbackFile = null;

        $this->assertSame($expectedSettings->shortTag, $settings->shortTag);
        $this->assertSame($expectedSettings->aspTags, $settings->aspTags);
        $this->assertSame($expectedSettings->parallelJobs, $settings->parallelJobs);
        $this->assertSame($expectedSettings->extensions, $settings->extensions);
        $this->assertSame($expectedSettings->paths, $settings->paths);
        $this->assertSame($expectedSettings->excluded, $settings->excluded);
        $this->assertSame($expectedSettings->colors, $settings->colors);
        $this->assertSame($expectedSettings->showProgress, $settings->showProgress);
        $this->assertSame($expectedSettings->format, $settings->format);
        $this->assertSame($expectedSettings->syntaxErrorCallbackFile, $settings->syntaxErrorCallbackFile);
    }

    public function testMoreArguments()
    {
        $commandLine = "./parallel-lint --exclude vendor --no-colors .";
        $argv = explode(" ", $commandLine);
        $settings = Settings::parseArguments($argv);

        $expectedSettings = new Settings();
        $expectedSettings->shortTag = false;
        $expectedSettings->aspTags = false;
        $expectedSettings->parallelJobs = 10;
        $expectedSettings->extensions = array('php', 'phtml', 'php3', 'php4', 'php5', 'phpt');
        $expectedSettings->paths = array('.');
        $expectedSettings->excluded = array('vendor');
        $expectedSettings->colors = Settings::DISABLED;
        $expectedSettings->showProgress = true;
        $expectedSettings->format = Settings::FORMAT_TEXT;
        $expectedSettings->showDeprecated = false;

        $this->assertSame($expectedSettings->shortTag, $settings->shortTag);
        $this->assertSame($expectedSettings->aspTags, $settings->aspTags);
        $this->assertSame($expectedSettings->parallelJobs, $settings->parallelJobs);
        $this->assertSame($expectedSettings->extensions, $settings->extensions);
        $this->assertSame($expectedSettings->paths, $settings->paths);
        $this->assertSame($expectedSettings->excluded, $settings->excluded);
        $this->assertSame($expectedSettings->colors, $settings->colors);
        $this->assertSame($expectedSettings->showProgress, $settings->showProgress);
        $this->assertSame($expectedSettings->format, $settings->format);
        $this->assertSame($expectedSettings->showDeprecated, $settings->showDeprecated);
    }

    public function testColorsForced()
    {
        $commandLine = "./parallel-lint --exclude vendor --colors .";
        $argv = explode(" ", $commandLine);
        $settings = Settings::parseArguments($argv);

        $expectedSettings = new Settings();
        $expectedSettings->colors = Settings::FORCED;

        $this->assertSame($expectedSettings->colors, $settings->colors);
    }

    public function testNoProgress()
    {
        $commandLine = "./parallel-lint --exclude vendor --no-progress .";
        $argv = explode(" ", $commandLine);
        $settings = Settings::parseArguments($argv);

        $expectedSettings = new Settings();
        $expectedSettings->showProgress = false;

        $this->assertSame($expectedSettings->showProgress, $settings->showProgress);
    }

    public function testJsonOutput()
    {
        $commandLine = './parallel-lint --json .';
        $argv = explode(" ", $commandLine);
        $settings = Settings::parseArguments($argv);
        $this->assertSame(Settings::FORMAT_JSON, $settings->format);
    }

    public function testGitLabOutput()
    {
        $commandLine = './parallel-lint --gitlab .';
        $argv = explode(" ", $commandLine);
        $settings = Settings::parseArguments($argv);
        $this->assertSame(Settings::FORMAT_GITLAB, $settings->format);
    }

    public function testCheckstyleOutput()
    {
        $commandLine = './parallel-lint --checkstyle .';
        $argv = explode(" ", $commandLine);
        $settings = Settings::parseArguments($argv);
        $this->assertSame(Settings::FORMAT_CHECKSTYLE, $settings->format);
    }

    public function testExtensions()
    {
        $commandLine = './parallel-lint -e php,php.dist,phpt .';
        $argv = explode(" ", $commandLine);
        $settings = Settings::parseArguments($argv);

        $expectedSettings = new Settings();
        $expectedSettings->extensions    = array('php', 'php.dist', 'phpt');

        $this->assertSame($expectedSettings->extensions, $settings->extensions);
    }

    public function testFailCallaback()
    {
        $commandLine = "./parallel-lint --syntax-error-callback ./path/to/my_custom_callback_file.php .";
        $argv = explode(" ", $commandLine);
        $settings = Settings::parseArguments($argv);

        $expectedSettings = new Settings();
        $expectedSettings->syntaxErrorCallbackFile = "./path/to/my_custom_callback_file.php";

        $this->assertSame($expectedSettings->syntaxErrorCallbackFile, $settings->syntaxErrorCallbackFile);
    }
}
