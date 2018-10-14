<?php

namespace Phpactor\Composer\Command;

use Composer\Composer;
use Composer\Installer;
use Composer\Repository\RepositoryInterface;
use Phpactor\Composer\PhpactorExtensionPackage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    protected function configure()
    {
        $this->setName('extension:list');
        $this->setDescription('List extensions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders([
            'Name',
            'Version',
            'Description',
        ]);
        foreach (PhpactorExtensionPackage::filter($this->repository->getPackages()) as $package) {
            $table->addRow([
                $package->getName(),
                $package->getFullPrettyVersion(),
                $package->getDescription()
            ]);
        }
        $table->render();
    }
}
