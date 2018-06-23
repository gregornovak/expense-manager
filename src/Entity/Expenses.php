<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExpensesRepository")
 */
class Expenses
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $added;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * Provide this value in cents
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    private $currency;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $cash;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $payee;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="expenses")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"additional"})
     * @MaxDepth(1)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ExpensesCategories", inversedBy="expenses")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"additional"})
     * @MaxDepth(1)
     */
    private $expenses_category;

    public function getId()
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

    public function getAdded(): ?\DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(\DateTimeInterface $added): self
    {
        $this->added = $added;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCash(): ?bool
    {
        return $this->cash;
    }

    public function setCash(?bool $cash): self
    {
        $this->cash = $cash;

        return $this;
    }

    public function getPayee(): ?string
    {
        return $this->payee;
    }

    public function setPayee(?string $payee): self
    {
        $this->payee = $payee;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getExpensesCategory(): ?ExpensesCategories
    {
        return $this->expenses_category;
    }

    public function setExpensesCategory(?ExpensesCategories $expenses_category): self
    {
        $this->expenses_category = $expenses_category;

        return $this;
    }
}
