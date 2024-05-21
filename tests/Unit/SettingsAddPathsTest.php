<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Tests\Unit;

use PHP_Parallel_Lint\PhpParallelLint\Settings;
use PHP_Parallel_Lint\PhpParallelLint\Tests\UnitTestCase;

/**
 * @covers \PHP_Parallel_Lint\PhpParallelLint\Settings::addPaths
 */
class SettingsAddPathsTest extends UnitTestCase
{
    /**
     * Test adding additional paths to the paths already in settings.
     *
     * @dataProvider dataAddPaths
     *
     * @param array $original The "original" value for $paths as set from the command-line.
     * @param array $extra    Extra paths to add.
     * @param array $expected Expected $paths.
     *
     * @return void
     */
    public function testAddPaths($original, $extra, $expected)
    {
        $settings        = new Settings();
        $settings->paths = $original;

        $settings->addPaths($extra);

        $this->assertSame($expected, $settings->paths);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataAddPaths()
    {
        return array(
            'No paths passed on CLI, no extra paths' => array(
                'original' => array(),
                'extra'    => array(),
                'expected' => array(),
            ),
            'No paths passed on CLI, extra paths' => array(
                'original' => array(),
                'extra'    => array('path/1', 'path/subdir/2'),
                'expected' => array('path/1', 'path/subdir/2'),
            ),
            'Paths passed on CLI, no extra paths' => array(
                'original' => array('path/1', 'path/subdir/2'),
                'extra'    => array(),
                'expected' => array('path/1', 'path/subdir/2'),
            ),
            'Paths passed on CLI, extra paths, partially duplicate' => array(
                'original' => array('path/1', 'path/subdir/2'),
                'extra'    => array('path/1', 'path/subdir/3'),
                'expected' => array('path/1', 'path/subdir/2', 'path/1', 'path/subdir/3'),
            ),
            'Paths passed on CLI, paths with spaces' => array(
                'original' => array('path with/spaces between/1', 'path with/spaces between/subdir/2'),
                'extra'    => array(
                    'path/1',
                    'path/subdir/3',
                    'path/with spaces',
                    'windows-path\with spaces\and\backslashes',
                    'windows path\with spaces\and\backslashes'
                ),
                'expected' => array(
                    'path with/spaces between/1',
                    'path with/spaces between/subdir/2',
                    'path/1',
                    'path/subdir/3',
                    'path/with spaces',
                    'windows-path\with spaces\and\backslashes',
                    'windows path\with spaces\and\backslashes',
                ),
            )
        );
    }
}
