<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Process;

use DateInterval;
use DateTime;
use DateTimeZone;
use PHP_Parallel_Lint\PhpParallelLint\Exceptions\RuntimeException;

class GitBlameProcess extends Process
{
    /**
     * @param string $gitExecutable
     * @param string $file
     * @param int $line
     * @throws RuntimeException
     */
    public function __construct($gitExecutable, $file, $line)
    {
        $arguments = array('blame', '-p', '-L', "$line,+1", $file);
        parent::__construct($gitExecutable, $arguments);
    }

    /**
     * @return bool
     * @throws RuntimeException
     */
    public function isSuccess()
    {
        return $this->getStatusCode() === 0;
    }

    /**
     * @return string
     * @throws RuntimeException
     */
    public function getAuthor()
    {
        if (!$this->isSuccess()) {
            throw new RuntimeException("Author can only be retrieved for successful process output.");
        }

        $output = $this->getOutput();
        preg_match('~^author (.*)~m', $output, $matches);
        return $matches[1];
    }

    /**
     * @return string
     * @throws RuntimeException
     */
    public function getAuthorEmail()
    {
        if (!$this->isSuccess()) {
            throw new RuntimeException("Author e-mail can only be retrieved for successful process output.");
        }

        $output = $this->getOutput();
        preg_match('~^author-mail <(.*)>~m', $output, $matches);
        return $matches[1];
    }

    /**
     * @return DateTime
     * @throws RuntimeException
     */
    public function getAuthorTime()
    {
        if (!$this->isSuccess()) {
            throw new RuntimeException("Author time can only be retrieved for successful process output.");
        }

        $output = $this->getOutput();

        preg_match('~^author-time (.*)~m', $output, $matches);
        $time = $matches[1];

        preg_match('~^author-tz (.*)~m', $output, $matches);
        $zone = $matches[1];

        return $this->getDateTime($time, $zone);
    }

    /**
     * @return string
     * @throws RuntimeException
     */
    public function getCommitHash()
    {
        if (!$this->isSuccess()) {
            throw new RuntimeException("Commit hash can only be retrieved for successful process output.");
        }

        return substr($this->getOutput(), 0, strpos($this->getOutput(), ' '));
    }

    /**
     * @return string
     * @throws RuntimeException
     */
    public function getSummary()
    {
        if (!$this->isSuccess()) {
            throw new RuntimeException("Commit summary can only be retrieved for successful process output.");
        }

        $output = $this->getOutput();
        preg_match('~^summary (.*)~m', $output, $matches);
        return $matches[1];
    }

    /**
     * @param string $gitExecutable
     * @return bool
     * @throws RuntimeException
     */
    public static function gitExists($gitExecutable)
    {
        $process = new Process($gitExecutable, array('--version'));
        $process->waitForFinish();
        return $process->getStatusCode() === 0;
    }

    /**
     * This harakiri method is required to correct support time zone in PHP 5.4
     *
     * @param int $time
     * @param string $zone
     * @return DateTime
     * @throws \Exception
     */
    protected function getDateTime($time, $zone)
    {
        $utcTimeZone = new DateTimeZone('UTC');
        $datetime = DateTime::createFromFormat('U', $time, $utcTimeZone);

        $way = substr($zone, 0, 1);
        $hours = (int) substr($zone, 1, 2);
        $minutes = (int) substr($zone, 3, 2);

        $interval = new DateInterval("PT{$hours}H{$minutes}M");

        if ($way === '+') {
            $datetime->add($interval);
        } else {
            $datetime->sub($interval);
        }

        return new DateTime($datetime->format('Y-m-d\TH:i:s') . $zone, $utcTimeZone);
    }
}
