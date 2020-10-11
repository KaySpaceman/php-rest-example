<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Customer
{
    /**
     * @var @MongoDB\Id
     */
    protected $id;

    /**
     * @var @MongoDB\Field(type="string")
     */
    protected $firstName;

    /**
     * @var @MongoDB\Field(type="string")
     */
    protected $lastName;

    /**
     * @var @MongoDB\Field(type="string")
     */
    protected $email;

    /**
     * @var @MongoDB\Field(type="string")
     */
    protected $address;

    /**
     * @var @MongoDB\Field(type="string")
     */
    protected $city;

    /**
     * @var @MongoDB\Field(type="string")
     */
    protected $gender;

    /**
     * @var @MongoDB\Field(type="string")
     */
    protected $ssn;

    /**
     * @var @MongoDB\Field(type="int")
     */
    protected $accountBalance;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return string|null
     */
    public function getSsn(): ?string
    {
        return $this->ssn;
    }

    /**
     * @param string $ssn
     */
    public function setSsn(string $ssn): void
    {
        $this->ssn = $ssn;
    }

    /**
     * @return int
     */
    public function getAccountBalance(): int
    {
        return $this->accountBalance ?? 0;
    }

    /**
     * @param int $accountBalance
     */
    public function setAccountBalance(int $accountBalance): void
    {
        $this->accountBalance = $accountBalance;
    }
}

