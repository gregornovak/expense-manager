<?php

namespace App\Entity;

use App\Security\UserRole;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Exclude;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank() 
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank() 
     */
    private $lastname;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank() 
	 * @Assert\Email()
	 * @Assert\NotBlank(groups={"login"})
     * @Assert\Email(groups={"login"}) 
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank() 
	 * @Assert\NotBlank(groups={"login"})
     * @Serializer\Groups({"hidden"})
     */
    private $password;

    /**
     * @ORM\Column(type="json")
     * @Exclude()
     */
	private $roles = [];
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	private $added;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $updated;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $lastLogin;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Expenses", mappedBy="user")
     * @Serializer\Groups({"additional"})
     * @Serializer\MaxDepth(1)
	*/
	private $expenses;

	/**
	 * Not used but must be declared because of the jwt library
	 */
	private $username;
    /**
    * @ORM\OneToMany(targetEntity="App\Entity\ExpensesCategories", mappedBy="user")
    * @Serializer\Groups({"additional"})
    * @Serializer\MaxDepth(1)
    */
    private $expensesCategories;

	public function __construct()
	{
		$this->expenses = new ArrayCollection();
        $this->expensesCategories = new ArrayCollection();
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function setFirstname(string $firstname): void
	{
		$this->firstname = $firstname;
	}

	public function getFirstname(): ?string
	{
		return $this->firstname;
	}

	public function setLastname(string $lastname): void
	{
		$this->lastname = $lastname;
	}

	public function getLastname(): ?string
	{
		return $this->lastname;
	}

	/**
	 * Not used but must be declared because of the jwt library
	 */
	public function setUsername(string $username): void
	{
		$this->username = $username;
	}
	/**
	 * Not used but must be declared because of the jwt library
	 */
	public function getUsername(): ?string
	{
		return $this->username;
	}

	public function setEmail(string $email): void
	{
		$this->email = $email;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	public function getPassword(): ?string
	{
		return $this->password;
	}

	public function getRoles(): array
	{
		$roles = $this->roles;

		if (empty($roles)) {
			$roles[] = UserRole::USER;
		}

		return array_unique($roles);
	}

	public function setRoles(array $roles): void
	{
		$this->roles = $roles;
	}

	public function getSalt(): ?string
	{
		return null;
	}

	public function eraseCredentials(): void
	{

	}

	public function setAdded(\DateTime $added): void
	{
		$this->added = $added;
	}

	public function getAdded(): ?\DateTime
	{
		return $this->added;
	}

	public function setUpdated(\DateTime $updated): void
	{
		$this->updated = $updated;
	}

	public function getUpdated(): ?\DateTime
	{
		return $this->updated;
	}

	public function setLastLogin(\DateTime $lastLogin): void
	{
		$this->lastLogin = $lastLogin;
	}

	public function getLastLogin(): ?\DateTime
	{
		return $this->lastLogin;
	}
	
	/**
	 * @return Collection|Expenses[]
	*/
	public function getExpenses(): Collection
	{
		return $this->expenses;
	}
	
	public function addExpense(Expenses $expense): self
	{
		if (!$this->expenses->contains($expense)) {
			$this->expenses[] = $expense;
			$expense->setUser($this);
		}
		
		return $this;
	}
	
	public function removeExpense(Expenses $expense): self
	{
		if ($this->expenses->contains($expense)) {
			$this->expenses->removeElement($expense);
			// set the owning side to null (unless already changed)
			if ($expense->getUser() === $this) {
				$expense->setUser(null);
			}
		}
		
		return $this;
	}

     /**
      * @return Collection|ExpensesCategories[]
      */
     public function getExpensesCategories(): Collection
     {
         return $this->expensesCategories;
     }

     public function addExpensesCategory(ExpensesCategories $expensesCategory): self
     {
         if (!$this->expensesCategories->contains($expensesCategory)) {
             $this->expensesCategories[] = $expensesCategory;
             $expensesCategory->setUser($this);
         }

         return $this;
     }

     public function removeExpensesCategory(ExpensesCategories $expensesCategory): self
     {
         if ($this->expensesCategories->contains($expensesCategory)) {
             $this->expensesCategories->removeElement($expensesCategory);
             // set the owning side to null (unless already changed)
             if ($expensesCategory->getUser() === $this) {
                 $expensesCategory->setUser(null);
             }
         }

         return $this;
     }
		
}
