<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Process;

use PHP_Parallel_Lint\PhpParallelLint\Exceptions\RuntimeException;

class SkipLintProcess extends PhpProcess
{
    /** @var array */
    private $skipped = array();

    /** @var bool */
    private $done = false;

    /** @var string */
    private $endLastChunk = '';

    /**
     * @param PhpExecutable $phpExecutable
     * @param array $filesToCheck
     * @throws RuntimeException
     */
    public function __construct(PhpExecutable $phpExecutable, array $filesToCheck)
    {
        $scriptPath = __DIR__ . '/../../bin/skip-linting.php';
        $script = file_get_contents($scriptPath);

        if (!$script) {
            throw new RuntimeException("skip-linting.php script not found in '$scriptPath'.");
        }

        $script = str_replace('<?php', '', $script);

        $parameters = array('-d', 'display_errors=stderr', '-r', $script);
        parent::__construct($phpExecutable, $parameters, implode(PHP_EOL, $filesToCheck));
    }

    /**
     * @throws RuntimeException
     */
    public function getChunk()
    {
        if (!$this->isFinished()) {
            $this->processLines(fread($this->stdout, 8192));
        }
    }

    /**
     * @return bool
     * @throws RuntimeException
     */
    public function isFinished()
    {
        $isFinished = parent::isFinished();
        if ($isFinished && !$this->done) {
            $this->done = true;
            $output = $this->getOutput();
            $this->processLines($output);
        }

        return $isFinished;
    }

    /**
     * @param string $file
     * @return bool|null
     */
    public function isSkipped($file)
    {
        if (isset($this->skipped[$file])) {
            return $this->skipped[$file];
        }

        return null;
    }

    /**
     * @param string $content
     */
    private function processLines($content)
    {
        if (!empty($content)) {
            $lines = explode(PHP_EOL, $this->endLastChunk . $content);
            $this->endLastChunk = array_pop($lines);
            foreach ($lines as $line) {
                $parts = explode(';', $line);
                list($file, $status) = $parts;
                $this->skipped[$file] = $status === '1' ? true : false;
            }
        }
    }
}
