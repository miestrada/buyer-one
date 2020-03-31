<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BuyerRepository")
 */
class Buyer implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     * @Assert\NotBlank(
     *     message="Should not be blank",
     * )
     * @Assert\Length(
     *     min=9,
     *     max=9,
     *     minMessage="Minimum lenght of {{ limit }} required",
     *     maxMessage="Maximum length of {{ limit }} required",
     * )
     * @Assert\Regex(
     *     pattern="/^(6|7)/i",
     *     message="Should start with 6 or 7",
     * )
     */
    private string $phone;

    /**
     * @ORM\Column(type="string", length=190, unique=true, nullable=true)
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=190, unique=true, nullable=true)
     */
    private ?string $password;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private ?string $token;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private ?int $code;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Address", mappedBy="buyer")
     */
    private $addresses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PaymentMethod", mappedBy="buyer")
     */
    private $paymentMethods;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sale", mappedBy="buyer")
     */
    private $sales;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->paymentMethods = new ArrayCollection();
        $this->sales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->phone;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Address[]
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setBuyer($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->contains($address)) {
            $this->addresses->removeElement($address);
            // set the owning side to null (unless already changed)
            if ($address->getBuyer() === $this) {
                $address->setBuyer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PaymentMethod[]
     */
    public function getPaymentMethods(): Collection
    {
        return $this->paymentMethods;
    }

    public function addPaymentMethod(PaymentMethod $paymentMethod): self
    {
        if (!$this->paymentMethods->contains($paymentMethod)) {
            $this->paymentMethods[] = $paymentMethod;
            $paymentMethod->setBuyer($this);
        }

        return $this;
    }

    public function removePaymentMethod(PaymentMethod $paymentMethod): self
    {
        if ($this->paymentMethods->contains($paymentMethod)) {
            $this->paymentMethods->removeElement($paymentMethod);
            // set the owning side to null (unless already changed)
            if ($paymentMethod->getBuyer() === $this) {
                $paymentMethod->setBuyer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sale[]
     */
    public function getSales(): Collection
    {
        return $this->sales;
    }

    public function addSale(Sale $sale): self
    {
        if (!$this->sales->contains($sale)) {
            $this->sales[] = $sale;
            $sale->setBuyer($this);
        }

        return $this;
    }

    public function removeSale(Sale $sale): self
    {
        if ($this->sales->contains($sale)) {
            $this->sales->removeElement($sale);
            // set the owning side to null (unless already changed)
            if ($sale->getBuyer() === $this) {
                $sale->setBuyer(null);
            }
        }

        return $this;
    }

}
