<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\WorkTimeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['worktime:read']],
    denormalizationContext: ['groups' => ['worktime:write']]
)]
#[ORM\Entity(repositoryClass: WorkTimeRepository::class)]
class WorkTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

//    #[Groups(['task:read', 'task:write', 'worktime:read', 'worktime:write'])]
//    #[Groups(['worktime:read', 'worktime:write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startTime = null;

//    #[Groups(['task:read', 'task:write', 'worktime:read', 'worktime:write'])]
//    #[Groups(['worktime:read', 'worktime:write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endTime = null;

//    #[Groups(['task:read', 'task:write', 'worktime:read', 'worktime:write'])]
//    #[Groups(['worktime:read', 'worktime:write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $creationDate = null;

//    #[Groups(['task:read', 'task:write', 'worktime:read', 'worktime:write'])]
//    #[Groups(['worktime:read', 'worktime:write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedDate = null;

//    #[Groups(['task:read', 'task:write', 'worktime:read', 'worktime:write'])]
//    #[Groups(['worktime:read', 'worktime:write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadline = null;

//    #[Groups(['task:read', 'task:write', 'worktime:read', 'worktime:write'])]
//    #[Groups(['task:read', 'task:write'])]
//    #[Groups(['worktime:read', 'worktime:write'])]
    #[ORM\OneToOne(targetEntity: Operation::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Operation $operation;

//    #[Groups(['task:read', 'task:write', 'worktime:read', 'worktime:write'])]
//    #[Groups(['worktime:read', 'worktime:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $notes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(?\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getUpdatedDate(): ?\DateTimeInterface
    {
        return $this->updatedDate;
    }

    public function setUpdatedDate(?\DateTimeInterface $updatedDate): static
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getOperation(): ?Operation
    {
        return $this->operation;
    }

    public function setOperation(?Operation $operation): self
    {
        $this->operation = $operation;
        return $this;
    }

}
