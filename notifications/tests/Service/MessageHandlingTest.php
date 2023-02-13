<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Handler\NewUserHandler;
use App\Message\NewUserMessage;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageHandlingTest extends KernelTestCase
{
    /**
     * Simulate that a message has been created in the queue.
     * Handle the message and confirm that the log entry is created.
     *
     * @test
     * @return void
     * @throws Exception
     */
    public function executeTest(): void
    {
        self::bootKernel();

        $user = new User();
        $user->setEmail('test@mail.bg');
        $user->setFirstName('Nikola');
        $user->setLastName('Kozhuharov');
        $message = new NewUserMessage($user);

        $messageBus = $this->getMockBuilder(MessageBusInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $messageBus->expects(self::exactly(1))
            ->method('dispatch')
            ->with($message)
            ->willReturn(new Envelope($message));
        $messageBus->dispatch($message);

        $handler = new NewUserHandler($this->getContainer()->get('logger'));
        $handler($message);

        foreach ($this->getContainer()->get('logger')->getHandlers() as $handler) {
            $handler->activate();
        }

        $logFilePath = dirname(__DIR__, 2) . '/var/log/test.log';
        $logContents = file_get_contents($logFilePath);
        unlink($logFilePath);

        $this->assertStringContainsString(
            'User created : Nikola Kozhuharov (test@mail.bg)',
            $logContents
        );
    }
}
