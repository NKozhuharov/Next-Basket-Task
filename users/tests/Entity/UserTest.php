<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function testToArray(): void
    {
        $user = new User();
        $user->setEmail('email@mail.bg');
        $user->setFirstName('first');
        $user->setLastName('last');

        $this->assertEquals(
            [
                'id'        => null,
                'email'     => 'email@mail.bg',
                'firstName' => 'first',
                'lastName'  => 'last',
            ],
            $user->toArray()
        );
    }
}
