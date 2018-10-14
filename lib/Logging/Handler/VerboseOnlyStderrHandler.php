<?php

namespace Phpactor\Logging\Handler;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Console\Output\OutputInterface;

class VerboseOnlyStderrHandler extends StreamHandler
{
    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output)
    {
        parent::__construct(STDERR);
        $this->output = $output;
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        if ($this->output->isVerbose()) {
            parent::write($record);
        }
    }
}
