<?php

namespace Rephpactor\Core;

use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\Installer;
use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\MapResolver\Resolver;
use Rephpactor\Composer\Command\InstallCommand;
use Rephpactor\Extension;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;

class CoreExtension implements Extension
{
    const TAG_COMMAND = 'core.command';

    public function configure(Resolver $resolver): void
    {
        $resolver->setDefaults([
            'core.extension.filename' => 'rephpactor-extensions.json',
        ]);
    }

    public function build(ContainerBuilder $container): void
    {
        $this->registerConsole($container);
    }

    private function registerConsole(ContainerBuilder $container)
    {
        $container->register('core.console.application', function (Container $container) {
            $application = new Application('Rephpactor');

            foreach ($container->getServiceIdsForTag(self::TAG_COMMAND) as $serviceId => $attrs) {
                $application->add($container->get($serviceId));
            }

            return $application;
        });

        $container->register('core.console.input', function () {
            return new ArgvInput();
        });

        $container->register('core.console.output', function () {
            return new StreamOutput(STDOUT);
        });
    }
}
