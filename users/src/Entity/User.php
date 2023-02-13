<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    /**
     * @codeCoverageIgnore
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @codeCoverageIgnore
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @codeCoverageIgnore
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @codeCoverageIgnore
     * @param string|null $firstName
     * @return $this
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @codeCoverageIgnore
     * @param string|null $lastName
     * @return $this
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'email'     => $this->email,
            'firstName' => $this->firstName,
            'lastName'  => $this->lastName,
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('email', new NotBlank());
        $metadata->addPropertyConstraint('email', new Email());
        $metadata->addPropertyConstraint('email', new Length(null, 1, 255));

        $metadata->addPropertyConstraint('firstName', new NotBlank());
        $metadata->addPropertyConstraint('firstName', new Length(null, 1, 255));

        $metadata->addPropertyConstraint('lastName', new NotBlank());
        $metadata->addPropertyConstraint('lastName', new Length(null, 1, 255));
    }
}
