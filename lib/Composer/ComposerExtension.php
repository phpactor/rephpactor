<?php

namespace Rephpactor\Composer;

use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\Installer;
use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\MapResolver\Resolver;
use Rephpactor\Composer\Command\InstallCommand;
use Rephpactor\Core\CoreExtension;
use Rephpactor\Extension;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;

class ComposerExtension implements Extension
{
    public function configure(Resolver $resolver): void
    {
        $resolver->setDefaults([
            'composer.extension.filename' => 'rephpactor-extensions.json',
        ]);
    }

    public function build(ContainerBuilder $container): void
    {
        $this->registerCommands($container);
        $this->registerComposer($container);
    }

    private function registerCommands(ContainerBuilder $container)
    {
        $container->register('composer.command.install-extension', function (Container $container) {
            return new InstallCommand($container->get('composer.installer'));
        }, [ CoreExtension::TAG_COMMAND => [] ]);
    }

    private function registerComposer(ContainerBuilder $container)
    {
        $container->register('composer.composer', function (Container $container) {
            return Factory::create($container->get('composer.io'), $container->getParameter('composer.extension.filename'));
        });
        
        $container->register('composer.installer', function (Container $container) {
            return Installer::create($container->get('composer.io'), $container->get('composer.composer'));
        });
        
        $container->register('composer.io', function (Container $container) {
            $helperSet  = new HelperSet([
                'question' => new QuestionHelper(),
            ]);
            return new ConsoleIO(
                $container->get('core.console.input'),
                $container->get('core.console.output'),
                $helperSet
            );
            
        });
    }
}
