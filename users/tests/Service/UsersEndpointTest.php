<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Message\NewUserMessage;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersEndpointTest extends WebTestCase
{
    private EntityManager $entityManager;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->client->enableProfiler();
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Test with an empty request body - expect error
     *
     * @test
     * @note We should make more tests for the validations, this is only a demonstration
     *
     * @return void
     */
    public function testEmptyRequest(): void
    {
        $this->client->request('POST', '/users');
        self::assertResponseIsUnprocessable();
        self::assertResponseStatusCodeSame(422);
        $this->assertEquals('{"email":"This value should not be blank.","firstName":"This value should not be blank.","lastName":"This value should not be blank."}',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Test with a valid request body.
     * Expect success.
     * Expect that a User will be created in the database.
     * Expect that a new message will be pushed in the queue.
     *
     * @test
     *
     * @return void
     * @throws Exception
     */
    public function testValidRequest(): void
    {
        $this->client->request(
            'POST',
            '/users',
            ['email' => 'test@mail.bg', 'firstName' => 'Nikola', 'lastName' => 'Kozhuharov']
        );
        self::assertResponseIsSuccessful();

        $users = $this->entityManager
            ->getRepository(User::class)
            ->findAll();
        $this->assertCount(1, $users);

        /** @var User $user */
        $user = $users[0];
        $this->assertEquals('test@mail.bg', $user->getEmail());
        $this->assertEquals('Nikola', $user->getFirstName());
        $this->assertEquals('Kozhuharov', $user->getLastName());

        $this->assertEquals(json_encode($user->toArray()), $this->client->getResponse()->getContent());

        $transport = $this->getContainer()->get('messenger.transport.async');
        $sentMessages = $transport->get();
        $this->assertCount(1, $sentMessages);

        $sentMessage = $sentMessages[0]->getMessage();
        $this->assertInstanceOf(NewUserMessage::class, $sentMessage);
        $this->assertEquals($user, $sentMessage->getUser());
    }
}
