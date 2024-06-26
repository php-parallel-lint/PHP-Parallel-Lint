<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Process;

use PHP_Parallel_Lint\PhpParallelLint\Exceptions\RuntimeException;

class PhpExecutable
{
    /** @var string */
    private $path;

    /**
     * Version as PHP_VERSION_ID constant
     * @var int
     */
    private $versionId;

    /** @var string */
    private $hhvmVersion;

    /** @var bool */
    private $isHhvmType;

    /**
     * @param string $path
     * @param int $versionId
     * @param string $hhvmVersion
     * @param bool $isHhvmType
     */
    public function __construct($path, $versionId, $hhvmVersion, $isHhvmType)
    {
        $this->path = $path;
        $this->versionId = $versionId;
        $this->hhvmVersion = $hhvmVersion;
        $this->isHhvmType = $isHhvmType;
    }

    /**
     * @return string
     */
    public function getHhvmVersion()
    {
        return $this->hhvmVersion;
    }

    /**
     * @return boolean
     */
    public function isIsHhvmType()
    {
        return $this->isHhvmType;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getVersionId()
    {
        return $this->versionId;
    }

    /**
     * @param string $phpExecutable
     * @return PhpExecutable
     * @throws \Exception
     */
    public static function getPhpExecutable($phpExecutable)
    {
        $codeToExecute = <<<PHP
echo 'PHP;', PHP_VERSION_ID, ';', defined('HPHP_VERSION') ? HPHP_VERSION : null;
PHP;

        $process = new Process($phpExecutable, array('-n', '-r', $codeToExecute));
        $process->waitForFinish();

        try {
            if ($process->getStatusCode() !== 0 && $process->getStatusCode() !== 255) {
                throw new RuntimeException("Unable to execute '{$phpExecutable}'.");
            }

            return self::getPhpExecutableFromOutput($phpExecutable, $process->getOutput());

        } catch (RuntimeException $e) {
            // Try HHVM type
            $process = new Process($phpExecutable, array('--php', '-r', $codeToExecute));
            $process->waitForFinish();

            if ($process->getStatusCode() !== 0 && $process->getStatusCode() !== 255) {
                throw new RuntimeException("Unable to execute '{$phpExecutable}'.");
            }

            return self::getPhpExecutableFromOutput($phpExecutable, $process->getOutput(), $isHhvmType = true);
        }
    }

    /**
     * @param string $phpExecutable
     * @param string $output
     * @param bool $isHhvmType
     * @return PhpExecutable
     * @throws RuntimeException
     */
    private static function getPhpExecutableFromOutput($phpExecutable, $output, $isHhvmType = false)
    {
        $parts = explode(';', $output);

        if ($parts[0] !== 'PHP' || !preg_match('~([0-9]+)~', $parts[1], $matches)) {
            throw new RuntimeException("'{$phpExecutable}' is not valid PHP binary.");
        }

        $hhvmVersion = isset($parts[2]) ? $parts[2] : false;

        return new PhpExecutable(
            $phpExecutable,
            intval($matches[1]),
            $hhvmVersion,
            $isHhvmType
        );
    }
}
