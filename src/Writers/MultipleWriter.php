<?php
namespace PHP_Parallel_Lint\PhpParallelLint\Writers;

class MultipleWriter implements WriterInterface
{
    /** @var WriterInterface[] */
    protected $writers;

    /**
     * @param WriterInterface[] $writers
     */
    public function __construct(array $writers)
    {
        foreach ($writers as $writer) {
            $this->addWriter($writer);
        }
    }

    /**
     * @param WriterInterface $writer
     */
    public function addWriter(WriterInterface $writer)
    {
        $this->writers[] = $writer;
    }

    /**
     * @param string $string
     */
    public function write($string)
    {
        foreach ($this->writers as $writer) {
            $writer->write($string);
        }
    }
}
