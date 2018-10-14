<?php

namespace Rephpactor;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\PhpactorContainer;
use Phpactor\MapResolver\Resolver;
use Rephpactor\Composer\ComposerExtension;
use Rephpactor\Core\CoreExtension;
use Rephpactor\Extension;
use Rephpactor\LanguageServer\LanguageServerExtension;
use Symfony\Component\Console\Input\ArgvInput;

class Rephpactor
{
    public function build(): Container
    {
        $parameters = [
            ComposerExtension::PARAM_EXTENSION_PATH => 'vendor-ext'
        ];
        $extensionAutoload = $parameters[ComposerExtension::PARAM_EXTENSION_PATH] . '/autoload.php';

        if (file_exists($extensionAutoload)) {
            require($extensionAutoload);
        }

        /** @var Extension[] $extensions */
        $extensions = [
            new CoreExtension(),
            new ComposerExtension(),
        ];

        foreach ($extensions as $extension) {
            $resolver = new Resolver();
            $extension->configure($resolver);
            $parameters = array_merge($parameters, $resolver->resolve([]));
        }

        $containerBuilder = new PhpactorContainer();

        foreach ($extensions as $extension) {
            $resolver = new Resolver();
            $extension->build($containerBuilder);
        }

        $container = $containerBuilder->build($parameters);

        foreach ($extensions as $extension) {
            $extension->initialize($container);
        }

        return $container;
    }
}
