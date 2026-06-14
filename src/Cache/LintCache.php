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

        $this->allEntries[$this->cacheKey] = $this->entries;

        $json = json_encode($this->allEntries);
        if ($json !== false && file_put_contents($this->cacheFilePath, $json) !== false) {
            $this->dirty = false;
        }
    }
}
