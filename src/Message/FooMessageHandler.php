<?php

namespace App\Message;

use Psr\Log\LoggerInterface;
// use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

// #[AsMessageHandler]
class FooMessageHandler implements MessageHandlerInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(FooMessage $message): void
    {
        sleep(10);
        $this->logger->info('Handling FooMessage', ['content' => $message->getContent()]);
    }
}
