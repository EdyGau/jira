<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
//use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['task:read']],
    denormalizationContext: ['groups' => ['task:write']]
)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "name", unique: true)]
    #[Groups(['task:read', 'task:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['task:read', 'task:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['task:read', 'task:write'])]
    private ?string $priority = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['task:read', 'task:write'])]
    private ?string $status = null;

    /**
     * @var Collection<int, Employee>
     */
    #[ORM\ManyToMany(targetEntity: Employee::class, mappedBy: 'tasks', cascade: ['persist'])]
    #[Groups(['task:read', 'task:write'])]
    private Collection $employees;

    #[ORM\OneToOne(targetEntity: WorkTime::class, mappedBy: 'task', cascade: ['persist', 'remove'])]
    #[Groups(['task:read', 'task:write'])]
    private ?WorkTime $workTime = null;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(?string $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Employee>
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addEmployee(Employee $employee): static
    {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            $employee->addTask($this);
        }

        return $this;
    }

    public function removeEmployee(Employee $employee): static
    {
        if ($this->employees->removeElement($employee)) {
            $employee->removeTask($this);
        }

        return $this;
    }

    public function getWorkTime(): ?WorkTime
    {
        return $this->workTime;
    }

    public function setWorkTime(?WorkTime $workTime): self
    {
        $this->workTime = $workTime;
        return $this;
    }

//    public function addEmployee(Employee $employee): self
//    {
//        if (!$this->employees->contains($employee)) {
//            $this->employees[] = $employee;
//        }
//        return $this;
//    }
//
//    public function removeEmployee(Employee $employee): self
//    {
//        $this->employees->removeElement($employee);
//        return $this;
//    }

    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Task';
    }
}
