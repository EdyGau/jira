<?php

namespace App\Entity;

use App\Repository\OperationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource(
    normalizationContext: ['groups' => ['operation:read']],
    denormalizationContext: ['groups' => ['operation:write']]
)]
#[ORM\Entity(repositoryClass: OperationRepository::class)]
class Operation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\JoinTable(name: "operation_operation_category")]
    #[ORM\ManyToMany(targetEntity: Operation::class, inversedBy: 'operations', cascade: ['persist'])]
    private Collection $operationCategories;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $outerId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadlineFrom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadlineTo = null;

    #[ORM\JoinTable(name: "operation_task")]
    #[ORM\ManyToMany(targetEntity: Task::class, inversedBy: 'operations', cascade: ['persist'])]
    private Collection $tasks;

//    TODO OneToMany???
    #[ORM\OneToOne(targetEntity: WorkTime::class, mappedBy: 'operation', cascade: ['persist', 'remove'])]
    private ?WorkTime $workTime = null;

    public function __construct()
    {
        $this->operationCategories = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOperationCategory(): ArrayCollection|Collection
    {
        return $this->operationCategories;
    }

    public function addOperationCategory(OperationCategory $operationCategories): self
    {
        if (!$this->operationCategories->contains($operationCategories)) {
            $this->operationCategories[] = $operationCategories;
        }
        return $this;
    }

    public function removeOperationCategory(OperationCategory $operationCategories): self
    {
        $this->operationCategories->removeElement($operationCategories);
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

    public function getDeadlineFrom(): ?\DateTimeInterface
    {
        return $this->deadlineFrom;
    }

    public function setDeadlineFrom(\DateTimeInterface $deadlineFrom): self
    {
        $this->deadlineFrom = $deadlineFrom;

        return $this;
    }

    public function getDeadlineTo(): ?\DateTimeInterface
    {
        return $this->deadlineTo;
    }

    public function setDeadlineTo(\DateTimeInterface $deadlineTo): self
    {
        $this->deadlineTo = $deadlineTo;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getTasks()
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

//    /**
//     * @return Collection|WorkTime[]
//     */
//    public function getWorkTimes(): Collection
//    {
//        return $this->workTimes;
//    }
//
//    public function addWorkTime(WorkTime $workTime): self
//    {
//        if (!$this->workTimes->contains($workTime)) {
//            $this->workTimes[] = $workTime;
//            $workTime->setOperation($this);
//        }
//
//        return $this;
//    }
//
//    public function removeWorkTime(WorkTime $workTime): self
//    {
//        if ($this->workTimes->removeElement($workTime)) {
//            if ($workTime->getOperation() === $this) {
//                $workTime->setOperation(null);
//            }
//        }
//
//        return $this;
//    }
}
