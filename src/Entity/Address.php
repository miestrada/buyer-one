<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 */
class Address
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Buyer", inversedBy="addresses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $buyer;

    /**
     * @ORM\Column(type="string", length=2)
     * @Assert\NotBlank(
     *     message="Should not be blank",
     * )
     * @Assert\Country(
     *     message="Not a valid country code",
     * )
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=12)
     * @Assert\NotBlank(
     *     message="Should not be blank",
     * )
     * @Assert\Regex(
     *     pattern="/^(?:0[1-9]|[1-4]\d|5[0-2])\d{3}$/i",
     *     message="Not a valid post code",
     * )
     */
    private $postCode;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=190)
     * @Assert\NotBlank(
     *     message="Should not be blank",
     * )
     * @Assert\Length(
     *     max=120,
     *     maxMessage="Maximum length of {{ limit }} required",
     * )
     */
    private $line1;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     * @Assert\Length(
     *     max=120,
     *     maxMessage="Maximum length of {{ limit }} required",
     * )
     */
    private $line2;

    /**
     * @ORM\Column(type="string", length=190)
     * @Assert\NotBlank(
     *     message="Should not be blank",
     * )
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sale", mappedBy="address")
     */
    private $sales;

    public function __construct()
    {
        $this->sales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuyer(): ?Buyer
    {
        return $this->buyer;
    }

    public function setBuyer(?Buyer $buyer): self
    {
        $this->buyer = $buyer;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(string $postCode): self
    {
        $this->postCode = $postCode;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getLine1(): ?string
    {
        return $this->line1;
    }

    public function setLine1(string $line1): self
    {
        $this->line1 = $line1;
        return $this;
    }

    public function getLine2(): ?string
    {
        return $this->line2;
    }

    public function setLine2(?string $line2): self
    {
        $this->line2 = $line2;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function cast(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'country' => $this->getCountry(),
            'post_code' => $this->getPostCode(),
            'line1' => $this->getLine1(),
            'line2' => $this->getLine2(),
            'city' => $this->getCity(),
            'state' => $this->getState(),
            'notes' => $this->getNotes(),
        ];
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
            $sale->setAddress($this);
        }

        return $this;
    }

    public function removeSale(Sale $sale): self
    {
        if ($this->sales->contains($sale)) {
            $this->sales->removeElement($sale);
            // set the owning side to null (unless already changed)
            if ($sale->getAddress() === $this) {
                $sale->setAddress(null);
            }
        }

        return $this;
    }
}
