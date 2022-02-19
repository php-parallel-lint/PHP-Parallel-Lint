<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit;

use PHP_Parallel_Lint\PhpParallelLint\Settings;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

class SettingsParseArgumentsTest extends UnitTestCase
{
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
    public function dataParseArguments()
    {
        return array(
            'No arguments other than the path' => array(
                'command'         => './parallel-lint .',
                'expectedChanged' => array(
                    'paths' => array('.'),
                ),
            ),
            'Multiple extensions, comma separated' => array(
                'command'         => './parallel-lint -e php,php.dist,phpt .',
                'expectedChanged' => array(
                    'extensions' => array('php', 'php.dist', 'phpt'),
                    'paths'      => array('.'),
                ),
            ),
            'Multiple arguments' => array(
                'command'         => './parallel-lint --exclude vendor --no-colors .',
                'expectedChanged' => array(
                    'excluded' => array('vendor'),
                    'colors'   => Settings::DISABLED,
                    'paths'    => array('.'),
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
