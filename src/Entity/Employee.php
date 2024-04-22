<?php

namespace App\Entity;

//use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AppAssert;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ApiResource(
//    collectionOperations: ['get', 'post'],
//    itemOperations: ['get', 'put', 'delete'],
    normalizationContext: ['groups' => ['employee:read']],
    denormalizationContext: ['groups' => ['employee:write']],
    order: ['email' => 'ASC']
)]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['employee:read', 'employee:write'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['employee:read', 'employee:write'])]
    private ?string $lastName = null;

    #[ORM\Column(name: "name", unique: true)]
    #[Groups(['employee:read', 'employee:write'])]
    #[Assert\Email]
    #[AppAssert\EmailConstraint]
    private ?string $email = null;

    #[ORM\JoinTable(name: "employee_task")]
    #[Groups(['employee:read', 'employee:write'])]
    #[ORM\ManyToMany(targetEntity: Task::class, inversedBy: 'employees', cascade: ['persist'])]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
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

    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
        }
        return $this;
    }

    public function removeTask(Task $task): self
    {
        $this->tasks->removeElement($task);
        return $this;
    }

}
