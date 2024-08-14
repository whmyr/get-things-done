<?php

declare(strict_types=1);

namespace Whmyr\Taskmanager\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class User extends AbstractEntity
{
    protected string $email;

    protected string $firstName;

    protected string $lastName;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName . ($this->firstName !== '' && $this->lastName !== '' ? ' ' : '') . $this->lastName;
    }
}
