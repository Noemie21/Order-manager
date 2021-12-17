<?php

namespace App\Entity;

use App\Repository\CommandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @ORM\Entity(repositoryClass=CommandRepository::class)
 */
class Command
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clientFirstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clientLastname;

    /**
     * @ORM\Column(type="string", length=400)
     */
    private $clientAddress;

    /**
     * @ORM\Column(type="integer")
     */
    private $clientPhoneNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clientMail;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dueDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Payment::class, mappedBy="command")
     */
    private $payments;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class)
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientFirstname(): ?string
    {
        return $this->clientFirstname;
    }

    public function setClientFirstname(string $clientFirstname): self
    {
        $this->clientFirstname = $clientFirstname;

        return $this;
    }

    public function getClientLastname(): ?string
    {
        return $this->clientLastname;
    }

    public function setClientLastname(string $clientLastname): self
    {
        $this->clientLastname = $clientLastname;

        return $this;
    }

    public function getClientAddress(): ?string
    {
        return $this->clientAddress;
    }

    public function setClientAddress(string $clientAddress): self
    {
        $this->clientAddress = $clientAddress;

        return $this;
    }

    public function getClientPhoneNumber(): ?int
    {
        return $this->clientPhoneNumber;
    }

    public function setClientPhoneNumber(int $clientPhoneNumber): self
    {
        $this->clientPhoneNumber = $clientPhoneNumber;

        return $this;
    }

    public function getClientMail(): ?string
    {
        return $this->clientMail;
    }

    public function setClientMail(string $clientMail): self
    {
        $this->clientMail = $clientMail;

        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function __toString() {
        return $this->id;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setCommand($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getCommand() === $this) {
                $payment->setCommand(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $this->products->removeElement($product);

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getTotal(): ?int
    {
        $total = 0;
        foreach ($this->products as $product) {
            $total += $product->getPrice();
        }
        return $total;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getTotalPaid(): ?int
    {
        $total = 0;
        foreach ($this->payments as $payment) {
            $total += $payment->getAmount();
        }
        return $total;
    }
}
