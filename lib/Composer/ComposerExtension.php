<?php

namespace Rephpactor\Composer;

use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\Installer;
use Composer\Json\JsonFile;
use Composer\Repository\CompositeRepository;
use Composer\Repository\FilesystemRepository;
use Composer\Repository\InstalledFilesystemRepository;
use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\MapResolver\Resolver;
use Rephpactor\Composer\Command\InstallCommand;
use Rephpactor\Composer\Command\ListCommand;
use Rephpactor\Core\CoreExtension;
use Rephpactor\Extension;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;

class ComposerExtension implements Extension
{
    const PARAM_EXTENSION_FILENAME = 'composer.extension.filename';
    const PARAM_EXTENSION_PATH = 'composer.extension_dirname';

    public function configure(Resolver $resolver): void
    {
        $resolver->setDefaults([
            self::PARAM_EXTENSION_FILENAME => __DIR__ . '/../../rephpactor-ext.json',
            self::PARAM_EXTENSION_PATH => __DIR__ . '/../../vendor-ext',
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

        $container->register('composer.command.list', function (Container $container) {
            return new ListCommand($container->get('composer.repository.local'));
        }, [ CoreExtension::TAG_COMMAND => [] ]);
    }

    private function registerComposer(ContainerBuilder $container)
    {
        $container->register('composer.composer', function (Container $container) {
            $originalComposerVendorDir = getenv('COMPOSER_VENDOR_DIR');
            $vendorDir = $container->getParameter(self::PARAM_EXTENSION_PATH);
            putenv('COMPOSER_VENDOR_DIR='.$vendorDir);

            $composer = Factory::create(
                $container->get('composer.io'),
                $container->getParameter(self::PARAM_EXTENSION_FILENAME)
            );

            putenv('COMPOSER_VENDOR_DIR='.$originalComposerVendorDir);

            return $composer;
        });
        
        $container->register('composer.installer', function (Container $container) {
            $installer = Installer::create($container->get('composer.io'), $container->get('composer.composer'));
            $installer->setAdditionalInstalledRepository($container->get('composer.repository.local'));

            return $installer;
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

        $container->register('composer.repository.local', function (Container $container) {
            return new CompositeRepository([
                new InstalledFilesystemRepository(new JsonFile(__DIR__ . '/../../vendor/composer/installed.json')),
            ]);
        });
    }

    public function initialize(Container $container): void
    {
        $path = $container->getParameter(self::PARAM_EXTENSION_FILENAME);

        if (!file_exists($path)) {
            file_put_contents($path, '{}');
        }
    }
}
