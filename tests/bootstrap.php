<?php

/**
 * Test bootstrap file.
 *
 * @phpcs:disable PSR1.Files.SideEffects
 */

namespace PHP_Parallel_Lint\PhpParallelLint\Tests;

if (is_dir(dirname(__DIR__) . '/vendor') && file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    $vendor_dir = dirname(__DIR__) . '/vendor';
} else {
    echo 'Please run `composer install` before attempting to run the unit tests.
You can still run the tests using a PHPUnit phar file, but the Composer autoload file needs to be available.
';
    die(1);
}

if (defined('__PHPUNIT_PHAR__')) {
    /*
     * Testing via a PHPUnit phar file.
     * Use a custom autoloader to allow for switching between PHP/PHPUnit version
     * without having to run `composer update` inbetween each test run.
     */
    spl_autoload_register(
        function ($className) {
            // Only try & load our own classes.
            if (stripos($className, 'PHP_Parallel_Lint\\PhpParallelLint\\') !== 0) {
                return false;
            }

            if (stripos($className, 'PHP_Parallel_Lint\\PhpParallelLint\\Tests\\') === 0) {
                // Strip namespace prefix 'PHP_Parallel_Lint\PhpParallelLint\Tests\'.
                $relativeClass = substr($className, 40);
                $file          = realpath(__DIR__ . '/' . strtr($relativeClass, '\\', '/') . '.php');
            } else {
                // Strip namespace prefix 'PHP_Parallel_Lint\PhpParallelLint\'.
                $relativeClass = substr($className, 34);
                $file          = realpath(dirname(__DIR__) . '/src/' . strtr($relativeClass, '\\', '/') . '.php');
            }

            if (file_exists($file)) {
                include_once $file;
            }

            return true;
        }
    );
} else {
    // Testing via a Composer setup.
    require_once $vendor_dir . '/autoload.php';
}

/*
 * Load the JsonSerializable polyfill.
 * This file is hard required in the CLI executable, but that file is not used in these tests.
 */
require_once dirname(__DIR__) . '/src/polyfill.php';

// Alias the PHPUnit 4/5 Exception class to its PHPUnit >= 6 name.
if (
    class_exists('PHPUnit_Framework_Exception') === true
    && class_exists('PHPUnit\Framework\Exception') === false
) {
    class_alias('PHPUnit_Framework_Exception', 'PHPUnit\Framework\Exception');
}

define('PL_TESTROOT', __DIR__);
