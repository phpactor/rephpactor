<?php

namespace Rephpactor;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\PhpactorContainer;
use Phpactor\MapResolver\Resolver;
use Rephpactor\Composer\ComposerExtension;
use Rephpactor\Core\CoreExtension;
use Rephpactor\Extension;
use Symfony\Component\Console\Input\ArgvInput;

class Rephpactor
{
    public function build(): Container
    {
        /** @var Extension $extension */
        $extensions = [
            new CoreExtension(),
            new ComposerExtension(),
        ];

        $parameters = [];
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

        return $containerBuilder->build($parameters);
    }
}
