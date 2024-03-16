<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit;

use PHP_Parallel_Lint\PhpParallelLint\Settings;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

/**
 * @covers \PHP_Parallel_Lint\PhpParallelLint\Settings::parseArguments
 * @covers \PHP_Parallel_Lint\PhpParallelLint\Iterators\ArrayIterator
 */
class SettingsParseArgumentsTest extends UnitTestCase
{
    /**
     * Test that an exception is thrown when an unsupported argument is passed.
     *
     * @dataProvider dataParseArgumentsInvalidArgument
     *
     * @param string $command     The command as received from the command line.
     * @param string $unsupported The unsupported argument which should trigger the exception.
     *
     * @return void
     */
    public function testParseArgumentsInvalidArgument($command, $unsupported)
    {
        $this->expectExceptionPolyfill('PHP_Parallel_Lint\PhpParallelLint\Exceptions\InvalidArgumentException');
        $this->expectExceptionMessagePolyfill('Invalid argument ' . $unsupported);

        $argv = explode(' ', $command);
        Settings::parseArguments($argv);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataParseArgumentsInvalidArgument()
    {
        return array(
            'Unsupported short argument' => array(
                'command'     => './parallel-lint --colors -u . --exclude vendor',
                'unsupported' => '-u',
            ),
            'Unsupported long argument' => array(
                'command'     => './parallel-lint . --no-progress --unsupported-arg',
                'unsupported' => '--unsupported-arg',
            ),
            'Unsupported argument split by = sign' => array(
                'command'     => './parallel-lint --exclude=vendor',
                'unsupported' => '--exclude=vendor',
            ),
        );
    }

    /**
     * Test parsing the arguments received from the command line.
     *
     * @dataProvider dataParseArguments
     *
     * @param string $command         The command as received from the command line.
     * @param array  $expectedChanged The Settings class properties which are expected to have been
     *                                changed (key) with their value.
     *
     * @return void
     */
    public function testParseArguments($command, array $expectedChanged)
    {
        $expected = $this->getExpectedSettings($expectedChanged);

        $argv     = explode(' ', $command);
        $settings = Settings::parseArguments($argv);

        $this->assertSame($expected, $this->getCurrentSettings($settings));
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataParseArguments()
    {
        return array(
            'No arguments at all' => array(
                'command'         => './parallel-lint',
                'expectedChanged' => array(),
            ),
            'No arguments other than the path' => array(
                'command'         => './parallel-lint .',
                'expectedChanged' => array(
                    'paths' => array('.'),
                ),
            ),
            'No arguments other than multiple paths in varying formats' => array(
                'command'         => './parallel-lint ./src /tests bin ' . __DIR__ . '/../absolute/',
                'expectedChanged' => array(
                    'paths' => array(
                        './src',
                        '/tests',
                        'bin',
                        __DIR__ . '/../absolute/',
                    ),
                ),
            ),
            'Custom path to PHP' => array(
                'command'         => 'parallel-lint -p path/to/php.exe .',
                'expectedChanged' => array(
                    'phpExecutable' => 'path/to/php.exe',
                    'paths'         => array('.'),
                ),
            ),
            'Multiple short arguments: -s -a -j 20' => array(
                'command'         => 'parallel-lint -s -a -j 20 .',
                'expectedChanged' => array(
                    'shortTag'     => true,
                    'aspTags'      => true,
                    'parallelJobs' => 20,
                    'paths'        => array('.'),
                ),
            ),
            'Multiple extensions, comma separated' => array(
                'command'         => './parallel-lint -e php,php.dist,phpt .',
                'expectedChanged' => array(
                    'extensions' => array('php', 'php.dist', 'phpt'),
                    'paths'      => array('.'),
                ),
            ),
            'Multiple long arguments' => array(
                'command'         => './parallel-lint --exclude vendor --short --asp .',
                'expectedChanged' => array(
                    'excluded' => array('vendor'),
                    'shortTag' => true,
                    'aspTags'  => true,
                    'paths'    => array('.'),
                ),
            ),
            'Multiple excludes, including subdir' => array(
                'command'         => './parallel-lint . --exclude .git --exclude node_modules --exclude tests/fixtures',
                'expectedChanged' => array(
                    'paths'    => array('.'),
                    'excluded' => array(
                        '.git',
                        'node_modules',
                        'tests/fixtures',
                    ),
                ),
            ),
            'Force enable colors' => array(
                'command'         => './parallel-lint --exclude vendor --colors .',
                'expectedChanged' => array(
                    'excluded' => array('vendor'),
                    'colors'   => Settings::FORCED,
                    'paths'    => array('.'),
                ),
            ),
            'Force disable colors' => array(
                'command'         => './parallel-lint --no-colors .',
                'expectedChanged' => array(
                    'colors'   => Settings::DISABLED,
                    'paths'    => array('.'),
                ),
            ),
            'No progress' => array(
                'command'         => './parallel-lint --exclude vendor --no-progress .',
                'expectedChanged' => array(
                    'excluded'     => array('vendor'),
                    'showProgress' => false,
                    'paths'        => array('.'),
                ),
            ),
            'Output Checkstyle' => array(
                'command'         => './parallel-lint --checkstyle .',
                'expectedChanged' => array(
                    'format' => Settings::FORMAT_CHECKSTYLE,
                    'paths'  => array('.'),
                ),
            ),
            'Output Json' => array(
                'command'         => './parallel-lint --json .',
                'expectedChanged' => array(
                    'format' => Settings::FORMAT_JSON,
                    'paths'  => array('.'),
                ),
            ),
            'Output GitLab' => array(
                'command'         => './parallel-lint --gitlab .',
                'expectedChanged' => array(
                    'format' => Settings::FORMAT_GITLAB,
                    'paths'  => array('.'),
                ),
            ),
            'Custom path to git' => array(
                'command'         => 'parallel-lint --git path/to/git.exe .',
                'expectedChanged' => array(
                    'gitExecutable' => 'path/to/git.exe',
                    'paths'         => array('.'),
                ),
            ),
            'Enable stdin' => array(
                'command'         => 'parallel-lint --stdin',
                'expectedChanged' => array(
                    'stdin' => true,
                ),
            ),
            'Enable blame' => array(
                'command'         => 'parallel-lint --blame .',
                'expectedChanged' => array(
                    'blame' => true,
                    'paths' => array('.'),
                ),
            ),
            'Ignore failures' => array(
                'command'         => 'parallel-lint --ignore-fails .',
                'expectedChanged' => array(
                    'ignoreFails' => true,
                    'paths'       => array('.'),
                ),
            ),
            'Show deprecations' => array(
                'command'         => 'parallel-lint --show-deprecated .',
                'expectedChanged' => array(
                    'showDeprecated' => true,
                    'paths'          => array('.'),
                ),
            ),
            'Callback file' => array(
                'command'         => './parallel-lint --syntax-error-callback ./path/to/my_custom_callback_file.php .',
                'expectedChanged' => array(
                    'syntaxErrorCallbackFile' => './path/to/my_custom_callback_file.php',
                    'paths'                   => array('.'),
                ),
            ),
        );
    }

    /**
     * Helper method: retrieve the default values of the properties in the Settings class and
     * use these to create the expected values of the properties.
     *
     * @param array $changed Changes to the settings compared to the default values.
     *
     * @return array
     */
    private function getExpectedSettings(array $changed)
    {
        $defaults = get_class_vars('\PHP_Parallel_Lint\PhpParallelLint\Settings');
        if (PHP_VERSION_ID >= 50400) {
            // phpcs:ignore PHPCompatibility.Constants.NewConstants.php_binaryFound
            $defaults['phpExecutable'] = PHP_BINARY;
        }

        return array_merge($defaults, $changed);
    }

    /**
     * Helper method: retrieve the current values of the properties in a Settings object.
     *
     * @param Settings $settings
     *
     * @return array
     */
    private function getCurrentSettings(Settings $settings)
    {
        return get_object_vars($settings);
    }
}
