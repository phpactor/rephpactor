<?php

namespace Rephpactor\Composer\Command;

use Composer\Composer;
use Composer\Installer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var Installer
     */
    private $installer;


    public function __construct(Installer $installer)
    {
        parent::__construct();
        $this->composer = $composer;
        $this->installer = $installer;
    }

    protected function configure()
    {
        $this->setName('extension:install');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $installer->run();
    }
}