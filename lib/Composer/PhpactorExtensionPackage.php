<?php

namespace Phpactor\Composer;

use Composer\Package\AliasPackage;
use Composer\Package\Package;
use Composer\Package\PackageInterface;

class PhpactorExtensionPackage
{
    const TYPE = 'phpactor-extension';
    const EXTRA_EXTENSION_CLASS = 'phpactor.extension_class';

    public static function filter(iterable $packages)
    {
        return array_filter($packages, function (PackageInterface $package) {
            if ($package instanceof AliasPackage) {
                return false;
            }

            return $package->getType() === self::TYPE;
        });
    }
}
