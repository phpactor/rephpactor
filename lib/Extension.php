<?php

namespace Rephpactor;

use Phpactor\Container\ContainerBuilder;
use Phpactor\MapResolver\Resolver;

interface Extension
{
    public function configure(Resolver $resolver): void;

    public function build(ContainerBuilder $container): void;
}
