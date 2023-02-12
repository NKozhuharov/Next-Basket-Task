<?php

namespace App\Handler;

use App\Message\NewUserMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NewUserHandler
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(NewUserMessage $newUserMessage): void
    {
        $user = $newUserMessage->getUser();

        $this->logger->info('User created ' . $user->toString());
    }
}
