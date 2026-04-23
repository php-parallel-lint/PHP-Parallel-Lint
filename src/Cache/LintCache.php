<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Cache;

class LintCache
{
    /** @var string */
    private $cacheFilePath;

    /** @var array All cache data keyed by settings key */
    private $allEntries = array();

    /** @var array Entries for the active cache key */
    private $entries = array();

    /** @var string */
    private $cacheKey = '';

    /** @var bool */
    private $dirty = false;

    const DEFAULT_CACHE_FILENAME = '.parallel-lint-cache.json';

    /**
     * @param string $cacheFilePath
     */
    public function __construct($cacheFilePath)
    {
        $this->cacheFilePath = $cacheFilePath;
    }

    /**
     * @param string|null $customPath
     * @return string
     */
    public static function getCacheFilePath($customPath)
    {
        if ($customPath !== null) {
            return $customPath;
        }

        $currentWorkingDirectory = getcwd();
        if ($currentWorkingDirectory === false) {
            $currentWorkingDirectory = '.';
        }

        return $currentWorkingDirectory . DIRECTORY_SEPARATOR . self::DEFAULT_CACHE_FILENAME;
    }

    /**
     * @param int $phpVersionId
     * @param bool $aspTags
     * @param bool $shortTag
     * @param bool $showDeprecated
     * @return string
     */
    public static function buildCacheKey($phpVersionId, $aspTags, $shortTag, $showDeprecated)
    {
        return 'php:' . $phpVersionId
            . '|asp:' . ($aspTags ? '1' : '0')
            . '|short:' . ($shortTag ? '1' : '0')
            . '|dep:' . ($showDeprecated ? '1' : '0');
    }

    /**
     * @param string $cacheKey
     * @return LintCache
     */
    public function load($cacheKey)
    {
        $this->cacheKey = $cacheKey;
        $this->allEntries = array();
        $this->entries = array();
        $this->dirty = false;

        if (!is_file($this->cacheFilePath)) {
            return $this;
        }

        $contents = file_get_contents($this->cacheFilePath);
        if ($contents === false) {
            return $this;
        }

        $data = json_decode($contents, true);
        if (!is_array($data)) {
            return $this;
        }

        $this->allEntries = $data;

        if (isset($this->allEntries[$cacheKey]) && is_array($this->allEntries[$cacheKey])) {
            $this->entries = $this->allEntries[$cacheKey];
        }

        return $this;
    }

    /**
     * @param array $files
     * @return array with keys 'uncached' and 'cached'
     */
    public function filterFiles(array $files)
    {
        // No cached entries to compare against, skip md5_file() on every file
        if (empty($this->entries)) {
            return array('uncached' => $files, 'cached' => array());
        }

        $uncached = array();
        $cached = array();

        foreach ($files as $file) {
            $checksum = $this->getFileChecksum($file);
            if ($checksum === false) {
                $uncached[] = $file;
                $this->evictEntry($file);
                continue;
            }

            if (isset($this->entries[$file]) && $this->entries[$file] === $checksum) {
                $cached[] = $file;
            } else {
                $uncached[] = $file;
            }
        }

        return array('uncached' => $uncached, 'cached' => $cached);
    }

    /**
     * @param string $filePath
     */
    public function recordSuccess($filePath)
    {
        $checksum = $this->getFileChecksum($filePath);
        if ($checksum === false) {
            return;
        }

        $this->entries[$filePath] = $checksum;
        $this->dirty = true;
    }

    /**
     * @param string $filePath
     */
    public function recordFailure($filePath)
    {
        $this->evictEntry($filePath);
    }

    /**
     * @param string $file
     * @return string|false
     */
    private function getFileChecksum($file)
    {
        if (!is_readable($file)) {
            return false;
        }

        return md5_file($file);
    }

    /**
     * @param string $file
     */
    private function evictEntry($file)
    {
        if (isset($this->entries[$file])) {
            unset($this->entries[$file]);
            $this->dirty = true;
        }
    }

    public function save()
    {
        if (!$this->dirty) {
            return;
        }

        $lockFile = $this->cacheFilePath . '.lock';
        $lockHandle = fopen($lockFile, 'cb');
        if ($lockHandle === false) {
            return;
        }

        if (!flock($lockHandle, LOCK_EX)) {
            fclose($lockHandle);
            return;
        }

        $this->mergeFromDisk();

        // Merge disk entries for the active key, with in-memory values taking precedence
        $diskEntries = array();
        if (isset($this->allEntries[$this->cacheKey]) && is_array($this->allEntries[$this->cacheKey])) {
            $diskEntries = $this->allEntries[$this->cacheKey];
        }
        $this->allEntries[$this->cacheKey] = array_merge($diskEntries, $this->entries);

        $json = json_encode($this->allEntries);
        if ($json !== false && $this->writeCacheFile($json)) {
            $this->dirty = false;
        }

        flock($lockHandle, LOCK_UN);
        fclose($lockHandle);
    }

    /**
     * Re-read cache file from disk to merge entries from concurrent runs
     */
    private function mergeFromDisk()
    {
        if (!is_file($this->cacheFilePath)) {
            return;
        }

        $contents = file_get_contents($this->cacheFilePath);
        if ($contents === false) {
            return;
        }

        $diskData = json_decode($contents, true);
        if (is_array($diskData)) {
            $this->allEntries = $diskData;
        }
    }

    /**
     * Atomically write cache data to disk via a temp file
     *
     * @param string $json
     * @return bool
     */
    private function writeCacheFile($json)
    {
        $tmpFile = $this->cacheFilePath . '.' . getmypid() . '.tmp';

        if (file_put_contents($tmpFile, $json, LOCK_EX) === false) {
            return false;
        }

        if (rename($tmpFile, $this->cacheFilePath)) {
            return true;
        }

        // Fallback for Windows where rename() fails if target exists
        if (file_exists($this->cacheFilePath) && !unlink($this->cacheFilePath)) {
            unlink($tmpFile);
            return false;
        }

        if (rename($tmpFile, $this->cacheFilePath)) {
            return true;
        }

        if (copy($tmpFile, $this->cacheFilePath)) {
            unlink($tmpFile);
            return true;
        }

        unlink($tmpFile);
        return false;
    }
}
