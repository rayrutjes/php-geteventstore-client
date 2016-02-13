<?php

namespace RayRutjes\GetEventStore;

final class UserCredentials
{
    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @param string $login
     * @param string $password
     */
    public function __construct(string $login, string $password)
    {
        if (empty($login)) {
            throw new \InvalidArgumentException(sprintf('Login should not be empty, got %s.', $login));
        }

        if (empty($password)) {
            throw new \InvalidArgumentException(sprintf('Password should not be empty.', $password));
        }

        $this->login = $login;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
