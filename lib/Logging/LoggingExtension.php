<?php

namespace Phpactor\Logging;

use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Extension;
use Phpactor\Logging\Handler\VerboseOnlyStderrHandler;
use Phpactor\MapResolver\Resolver;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Logger\ConsoleLogger;

class LoggingExtension implements Extension
{
    const SERVICE_LOGGER = 'logging.logger';

    const PARAM_LOGGING_ENABLED = 'logging.enabled';
    const PARAM_LOGGING_FINGERS_CROSSED = 'logging.fingers_crossed';
    const PARAM_LOGGING_LEVEL = 'logging.level';
    const PARAM_LOGGING_PATH = 'logging.path';

    public function configure(Resolver $schema): void
    {
        $schema->setDefaults([
            self::PARAM_LOGGING_ENABLED => false,
            self::PARAM_LOGGING_FINGERS_CROSSED => true,
            self::PARAM_LOGGING_PATH => 'phpactor.log',
            self::PARAM_LOGGING_LEVEL => LogLevel::WARNING,
        ]);
    }

    public function build(ContainerBuilder $container): void
    {
        $container->register(self::SERVICE_LOGGER, function (Container $container) {
            $logger = new Logger('phpactor');
            $logger->pushHandler(new VerboseOnlyStderrHandler(
                $container->get('core.console.output')
            ));

            if (false === $container->getParameter(self::PARAM_LOGGING_ENABLED)) {
                return $logger;
            }

            $handler = new StreamHandler(
                $container->getParameter(self::PARAM_LOGGING_PATH),
                $container->getParameter(self::PARAM_LOGGING_LEVEL)
            );

            if ($container->getParameter(self::PARAM_LOGGING_FINGERS_CROSSED)) {
                $handler = new FingersCrossedHandler($handler);
            }

            $logger->pushHandler($handler);

            return $logger;
        });
    }

    public function initialize(Container $container): void
    {
    }
}
