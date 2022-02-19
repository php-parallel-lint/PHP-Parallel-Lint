<?php
namespace PHP_Parallel_Lint\PhpParallelLint\Outputs;

use PHP_Parallel_Lint\PhpParallelLint\Settings;
use PHP_Parallel_Lint\PhpParallelLint\Writers\WriterInterface;

class TextOutputColored extends TextOutput
{
    /** @var \PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor|\JakubOnderka\PhpConsoleColor\ConsoleColor */
    private $colors;

    public function __construct(WriterInterface $writer, $colors = Settings::AUTODETECT)
    {
        parent::__construct($writer);

        if (class_exists('\PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor')) {
            $this->colors = new \PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor();
            $this->colors->setForceStyle($colors === Settings::FORCED);
        } else if (class_exists('\JakubOnderka\PhpConsoleColor\ConsoleColor')) {
            $this->colors = new \JakubOnderka\PhpConsoleColor\ConsoleColor();
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
            !$this->colors instanceof \PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor
            && !$this->colors instanceof \JakubOnderka\PhpConsoleColor\ConsoleColor
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