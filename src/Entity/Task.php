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
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'tasks', cascade: ['persist'])]
    #[Groups(['task:read', 'task:write'])]
    private Collection $users;

    #[ORM\OneToOne(targetEntity: WorkTime::class, mappedBy: 'task', cascade: ['persist', 'remove'])]
    #[Groups(['task:read', 'task:write'])]
    private ?WorkTime $workTime = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    public function getWorkTime(): ?WorkTime
    {
        return $this->workTime;
    }

    public function setWorkTime(?WorkTime $workTime): self
    {
        $this->workTime = $workTime;
        return $this;
    }

//    public function addUser(User $user): self
//    {
//        if (!$this->users->contains($user)) {
//            $this->users[] = $user;
//        }
//        return $this;
//    }
//
//    public function removeUser(User $user): self
//    {
//        $this->users->removeElement($user);
//        return $this;
//    }

    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Task';
    }
}
