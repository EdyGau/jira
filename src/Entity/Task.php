<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
//use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $outerId = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $productionOrderNumber = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $deadlineFrom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $deadlineTo = null;

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
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'tasks', cascade: ['persist'])]
    #[Groups(['task:read', 'task:write'])]
    private Collection $users;

    /**
     * @var Collection<int, Operation>
     */
    #[ORM\ManyToMany(targetEntity: Operation::class, mappedBy: 'tasks', cascade: ['persist'])]
    #[Groups(['task:read', 'task:write'])]
    private Collection $operations;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->operations = new ArrayCollection();
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
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addTask($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeTask($this);
        }

        return $this;
    }

    public function addOperation(Operation $operation): static
    {
        if (!$this->operations->contains($operation)) {
            $this->operations->add($operation);
            $operation->addTask($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): static
    {
        if ($this->operations->removeElement($operation)) {
            $operation->removeTask($this);
        }

        return $this;
    }

    public function getOuterId(): ?int
    {
        return $this->outerId;
    }

    public function setOuterId(string $outerId): self
    {
        $this->outerId = $outerId;

        return $this;
    }

    public function getProductionOrderNumber(): ?string
    {
        return $this->productionOrderNumber;
    }

    public function setProductionOrderNumber(?string $productionOrderNumber): void
    {
        $this->productionOrderNumber = $productionOrderNumber;
    }

    public function getOperations(): ?string
    {
        return $this->operations;
    }

    public function setOperations(?string $operations): void
    {
        $this->operations = $operations;
    }

    public function getDeadlineFrom(): ?\DateTimeInterface
    {
        return $this->deadlineFrom;
    }

    public function setDeadlineFrom(?\DateTimeInterface $deadlineFrom): void
    {
        $this->deadlineFrom = $deadlineFrom;
    }

    public function getDeadlineTo(): ?\DateTimeInterface
    {
        return $this->deadlineTo;
    }

    public function setDeadlineTo(?\DateTimeInterface $deadlineTo): void
    {
        $this->deadlineTo = $deadlineTo;
    }

    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Task';
    }
}
