<?php

namespace RayRutjes\GetEventStore\Test\Unit;

use RayRutjes\GetEventStore\Test\TestCase;
use RayRutjes\GetEventStore\UserCredentials;

class UserCredentialsTest extends TestCase
{
    public function testGivesAccessToLoginAndPassword()
    {
        $credentials = new UserCredentials('login', 'password');
        $this->assertEquals('login', $credentials->getLogin());
        $this->assertEquals('password', $credentials->getPassword());
    }

    /**
     * @dataProvider wrongCredentials
     * @expectedException \InvalidArgumentException
     */
    public function testValidatesInput($login, $password)
    {
        new UserCredentials($login, $password);
    }

    public function wrongCredentials()
    {
        return [
            ['', 'password'],
            ['login', ''],
        ];
    }
}
