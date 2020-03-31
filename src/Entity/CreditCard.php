<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CreditCardRepository")
 */
class CreditCard extends PaymentMethod implements PaymentMethodInterface
{
    protected string $type = 'CreditCard';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Should not be blank",
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=16)
     * @Assert\NotBlank(
     *     message="Should not be blank",
     * )
     * @Assert\CardScheme(
     *     schemes={"VISA"},
     *     message="Your credit card number is invalid"
     * )
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=7)
     * @Assert\NotBlank(
     *     message="Should not be blank",
     * )
     * @Assert\Regex(
     *     pattern="/^([12][0-9]{3})(0[1-9]|1[0-2])$/i",
     *     message="Not a valid YYYYMM date",
     * )
     */
    private $expires;

    /**
     * @ORM\Column(type="string", length=3)
     * @Assert\NotBlank(
     *     message="Should not be blank",
     * )
     * @Assert\Regex(
     *     pattern="/^([0-9]{3})$/i",
     *     message="Not a valid CVV code.",
     * )
     */
    private $cvv;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNumber(bool $safe = true): ?string
    {
        return $safe && strlen($this->number) === 16 ? str_repeat('*', 12) . substr($this->number, -4) : $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getExpires(): ?string
    {
        return $this->expires;
    }

    public function setExpires(string $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    public function getCvv(bool $safe = true): ?string
    {
        return $safe && strlen($this->cvv) === 3 ? '***' : $this->cvv;
    }

    public function setCvv(string $cvv): self
    {
        $this->cvv = $cvv;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function cast(): array
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'name' => $this->getName(),
            'number' => $this->getNumber(),
            'expires' => $this->getExpires(),
            'cvv' => $this->getCvv(),
        ];
    }
}
