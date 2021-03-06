<?php

namespace Phpactor\Composer;

use Composer\EventDispatcher\ScriptExecutionException;
use Composer\Package\Package;
use Composer\Package\PackageInterface;

class ExtensionWriter
{
    /**
     * @var string
     */
    private $extensionListFile;

    public function __construct(string $extensionListFile)
    {
        $this->extensionListFile = $extensionListFile;
    }

    /**
     * @param PackageInterface[] $packages
     */
    public function writeExtensionList(iterable $packages)
    {
        $packages = PhpactorExtensionPackage::filter($packages);

        $code = [
            '<?php',
            '// ' . date('c'),
            '// this file is autogenerated by phpactor do not edit it',
            '',
            'return ['
        ];

        foreach ($packages as $package) {
            $className = $this->classNameForPackage($package);
            $code[] = sprintf('  %s::class,', $this->classNameForPackage($package));
        }

        $code[] = '];';

        if (!file_exists(dirname($this->extensionListFile))) {
            mkdir(dirname($this->extensionListFile), 0777, true);
        }

        file_put_contents($this->extensionListFile, implode(PHP_EOL, $code));
    }

    private function classNameForPackage(PackageInterface $package)
    {
        $extra = $package->getExtra();

        if (!isset($extra[PhpactorExtensionPackage::EXTRA_EXTENSION_CLASS])) {
            throw new ScriptExecutionException(sprintf(
                'Phpactor Package "%s" has no "%s" in the extra section. This parameter must define the extensions class',
                $package->getName(),
                PhpactorExtensionPackage::EXTRA_EXTENSION_CLASS
            ));
        }

        return $extra[PhpactorExtensionPackage::EXTRA_EXTENSION_CLASS];
    }
}
