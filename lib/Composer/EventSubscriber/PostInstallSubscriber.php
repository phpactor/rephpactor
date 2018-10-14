<?php

namespace Phpactor\Composer\EventSubscriber;

use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvents;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Phpactor\Composer\ExtensionWriter;
use Phpactor\Composer\PhpactorExtensionPackage;

class PostInstallSubscriber implements EventSubscriberInterface
{
    /**
     * @var ExtensionWriter
     */
    private $extensionWriter;

    public function __construct(ExtensionWriter $extensionWriter)
    {
        $this->extensionWriter = $extensionWriter;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'handlePostPackageInstall',
        ];
    }

    public function handlePostPackageInstall(Event $event)
    {
        $repository = $event->getComposer()->getRepositoryManager()->getLocalRepository();
        $this->extensionWriter->writeExtensionList($repository->getPackages());
    }
}
