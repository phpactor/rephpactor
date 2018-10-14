<?php

namespace Phpactor;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\LanguageServer\LanguageServerExtension;
use Phpactor\Logging\LoggingExtension;
use Phpactor\MapResolver\Resolver;
use Phpactor\Composer\ComposerExtension;
use Phpactor\Core\CoreExtension;
use Phpactor\Extension;
use Symfony\Component\Console\Input\ArgvInput;

class Rephpactor
{
    public function build(): Container
    {
        $parameters = [
            ComposerExtension::PARAM_EXTENSION_PATH => 'vendor-ext'
        ];

        $this->bootstrapExtensionAutoload($parameters);

        /** @var Extension[] $extensions */
        $extensions = [
            new CoreExtension(),
            new ComposerExtension(),
            new LoggingExtension(),
            new LanguageServerExtension(),
        ];

        $parameters = $this->resolveParameters($extensions, $parameters);
        $container  = $this->buildContainer($extensions, $parameters);
        $container  = $this->initialize($extensions, $container);

        return $container;
    }

    private function resolveParameters(array $extensions, array $parameters): array
    {
        foreach ($extensions as $extension) {
            $resolver = new Resolver();
            $extension->configure($resolver);
            $parameters = array_merge($parameters, $resolver->resolve([]));
        }

        return $parameters;
    }

    private function buildContainer(array $extensions, array $parameters): Container
    {
        $containerBuilder = new PhpactorContainer();
        
        foreach ($extensions as $extension) {
            $extension->build($containerBuilder);
        }
        
        return $containerBuilder->build($parameters);
    }

    private function initialize(array $extensions, Container $container): Container
    {
        foreach ($extensions as $extension) {
            $extension->initialize($container);
        }

        return $container;
    }

    private function bootstrapExtensionAutoload(array $parameters)
    {
        $extensionAutoload = $parameters[ComposerExtension::PARAM_EXTENSION_PATH] . '/autoload.php';
        
        if (file_exists($extensionAutoload)) {
            require($extensionAutoload);
        }
    }
}
