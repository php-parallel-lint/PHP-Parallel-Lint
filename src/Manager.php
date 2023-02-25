<?php

namespace PHP_Parallel_Lint\PhpParallelLint;

use FilesystemIterator;
use PHP_Parallel_Lint\PhpParallelLint\Contracts\SyntaxErrorCallback;
use PHP_Parallel_Lint\PhpParallelLint\Errors\SyntaxError;
use PHP_Parallel_Lint\PhpParallelLint\Exceptions\CallbackNotImplementedException;
use PHP_Parallel_Lint\PhpParallelLint\Exceptions\ClassNotFoundException;
use PHP_Parallel_Lint\PhpParallelLint\Exceptions\ParallelLintException;
use PHP_Parallel_Lint\PhpParallelLint\Exceptions\PathNotFoundException;
use PHP_Parallel_Lint\PhpParallelLint\Iterators\FilteredRecursiveDirectoryIterator;
use PHP_Parallel_Lint\PhpParallelLint\Iterators\RecursiveDirectoryFilterIterator;
use PHP_Parallel_Lint\PhpParallelLint\Outputs\CheckstyleOutput;
use PHP_Parallel_Lint\PhpParallelLint\Outputs\GitLabOutput;
use PHP_Parallel_Lint\PhpParallelLint\Outputs\JsonOutput;
use PHP_Parallel_Lint\PhpParallelLint\Outputs\OutputInterface;
use PHP_Parallel_Lint\PhpParallelLint\Outputs\TextOutput;
use PHP_Parallel_Lint\PhpParallelLint\Outputs\TextOutputColored;
use PHP_Parallel_Lint\PhpParallelLint\Process\GitBlameProcess;
use PHP_Parallel_Lint\PhpParallelLint\Process\PhpExecutable;
use PHP_Parallel_Lint\PhpParallelLint\Writers\ConsoleWriter;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class Manager
{
    /** @var OutputInterface */
    protected $output;

    /**
     * @param null|Settings $settings
     * @return Result
     * @throws ParallelLintException
     */
    public function run(Settings $settings = null)
    {
        $settings = $settings ?: new Settings();
        $output = $this->output ?: $this->getDefaultOutput($settings);

        $phpExecutable = PhpExecutable::getPhpExecutable($settings->phpExecutable);
        $olderThanPhp54 = $phpExecutable->getVersionId() < 50400; // From PHP version 5.4 are tokens translated by default
        $translateTokens = $phpExecutable->isIsHhvmType() || $olderThanPhp54;

        $output->writeHeader($phpExecutable->getVersionId(), $settings->parallelJobs, $phpExecutable->getHhvmVersion());

        $files = $this->getFilesFromPaths($settings->paths, $settings->extensions, $settings->excluded);

        if (empty($files)) {
            throw new ParallelLintException('No file found to check.');
        }

        $output->setTotalFileCount(count($files));

        $parallelLint = new ParallelLint($phpExecutable, $settings->parallelJobs);
        $parallelLint->setAspTagsEnabled($settings->aspTags);
        $parallelLint->setShortTagEnabled($settings->shortTag);
        $parallelLint->setShowDeprecated($settings->showDeprecated);
        $parallelLint->setSyntaxErrorCallback($this->createSyntaxErrorCallback($settings));

        $parallelLint->setProcessCallback(function ($status, $file) use ($output) {
            if ($status === ParallelLint::STATUS_OK) {
                $output->ok();
            } elseif ($status === ParallelLint::STATUS_SKIP) {
                $output->skip();
            } elseif ($status === ParallelLint::STATUS_ERROR) {
                $output->error();
            } else {
                $output->fail();
            }
        });

        $result = $parallelLint->lint($files);

        if ($settings->blame) {
            $this->gitBlame($result, $settings);
        }

        $output->writeResult($result, new ErrorFormatter($settings->colors, $translateTokens), $settings->ignoreFails);

        return $result;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param Settings $settings
     * @return OutputInterface
     */
    protected function getDefaultOutput(Settings $settings)
    {
        $writer = new ConsoleWriter();
        switch ($settings->format) {
            case Settings::FORMAT_JSON:
                return new JsonOutput($writer);
            case Settings::FORMAT_GITLAB:
                return new GitLabOutput($writer);
            case Settings::FORMAT_CHECKSTYLE:
                return new CheckstyleOutput($writer);
        }

        if ($settings->colors === Settings::DISABLED) {
            $output = new TextOutput($writer);
        } else {
            $output = new TextOutputColored($writer, $settings->colors);
        }

        $output->showProgress = $settings->showProgress;

        return $output;
    }

    /**
     * @param Result $result
     * @param Settings $settings
     * @throws ParallelLintException
     */
    protected function gitBlame(Result $result, Settings $settings)
    {
        if (!GitBlameProcess::gitExists($settings->gitExecutable)) {
            return;
        }

        foreach ($result->getErrors() as $error) {
            if ($error instanceof SyntaxError) {
                $process = new GitBlameProcess($settings->gitExecutable, $error->getFilePath(), $error->getLine());
                $process->waitForFinish();

                if ($process->isSuccess()) {
                    $blame = new Blame();
                    $blame->name = $process->getAuthor();
                    $blame->email = $process->getAuthorEmail();
                    $blame->datetime = $process->getAuthorTime();
                    $blame->commitHash = $process->getCommitHash();
                    $blame->summary = $process->getSummary();

                    $error->setBlame($blame);
                }
            }
        }
    }

    /**
     * @param array $paths
     * @param array $extensions
     * @param array $excluded
     * @return array
     * @throws PathNotFoundException
     */
    protected function getFilesFromPaths(array $paths, array $extensions, array $excluded = array())
    {
        $extensions = array_map('preg_quote', $extensions, array_fill(0, count($extensions), '`'));
        $regex = '`\.(?:' . implode('|', $extensions) . ')$`iD';
        $files = array();

        foreach ($paths as $path) {
            if (is_file($path)) {
                $files[] = $path;
            } elseif (is_dir($path)) {
                $iterator = new FilteredRecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS, $excluded);
                if (!empty($excluded)) {
                    $iterator = new RecursiveDirectoryFilterIterator($iterator, $excluded);
                }
                $iterator = new RecursiveIteratorIterator(
                    $iterator,
                    RecursiveIteratorIterator::LEAVES_ONLY,
                    RecursiveIteratorIterator::CATCH_GET_CHILD
                );

                $iterator = new RegexIterator($iterator, $regex);

                /** @var \SplFileInfo[] $iterator */
                foreach ($iterator as $directoryFile) {
                    $files[] = (string) $directoryFile;
                }
            } else {
                throw new PathNotFoundException($path);
            }
        }

        $files = array_unique($files);

        return $files;
    }

    protected function createSyntaxErrorCallback(Settings $settings)
    {
        if ($settings->syntaxErrorCallbackFile === null) {
            return null;
        }

        $fullFilePath = realpath($settings->syntaxErrorCallbackFile);
        if ($fullFilePath === false) {
            throw new PathNotFoundException($settings->syntaxErrorCallbackFile);
        }

        require_once $fullFilePath;

        $expectedClassName = basename($fullFilePath, '.php');
        if (!class_exists($expectedClassName)) {
            throw new ClassNotFoundException($expectedClassName, $settings->syntaxErrorCallbackFile);
        }

        $callbackInstance = new $expectedClassName();

        if (!($callbackInstance instanceof SyntaxErrorCallback)) {
            throw new CallbackNotImplementedException($expectedClassName);
        }

        return $callbackInstance;
    }
}
