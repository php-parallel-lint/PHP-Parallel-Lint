<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Outputs;

use JakubOnderka\PhpConsoleColor\ConsoleColor as OldConsoleColor;
use PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor;
use PHP_Parallel_Lint\PhpParallelLint\Settings;
use PHP_Parallel_Lint\PhpParallelLint\Writers\WriterInterface;

class TextOutputColored extends TextOutput
{
    /** @var ConsoleColor|OldConsoleColor */
    private $colors;

    public function __construct(WriterInterface $writer, $colors = Settings::AUTODETECT)
    {
        parent::__construct($writer);

        if (class_exists('\PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor')) {
            $this->colors = new ConsoleColor();
            $this->colors->setForceStyle($colors === Settings::FORCED);
        } elseif (class_exists('\JakubOnderka\PhpConsoleColor\ConsoleColor')) {
            $this->colors = new OldConsoleColor();
            $this->colors->setForceStyle($colors === Settings::FORCED);
        }
    }

    /**
     * @param string $string
     * @param string $type
     * @throws \PHP_Parallel_Lint\PhpConsoleColor\InvalidStyleException|\JakubOnderka\PhpConsoleColor\InvalidStyleException
     */
    public function write($string, $type = self::TYPE_DEFAULT)
    {
        if (
            !$this->colors instanceof ConsoleColor
            && !$this->colors instanceof OldConsoleColor
        ) {
            parent::write($string, $type);
        } else {
            switch ($type) {
                case self::TYPE_OK:
                    parent::write($this->colors->apply('bg_green', $string));
                    break;

                case self::TYPE_SKIP:
                    parent::write($this->colors->apply('bg_yellow', $string));
                    break;

                case self::TYPE_ERROR:
                    parent::write($this->colors->apply('bg_red', $string));
                    break;

                default:
                    parent::write($string);
            }
        }
    }
}
