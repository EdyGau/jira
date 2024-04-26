<?php

namespace App\Entity;

use App\Repository\OperationCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource(
    normalizationContext: ['groups' => ['operation_category:read']],
    denormalizationContext: ['groups' => ['operation_category:write']]
)]
#[ORM\Entity(repositoryClass: OperationCategoryRepository::class)]
class OperationCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;


    #[ORM\Column(name: "operation_number_from", type: "integer", nullable: false)]
    private ?int $operationNumberFrom = null;

    #[ORM\Column(name: "operation_number_to", type: "integer", nullable: true)]
    private ?int $operationNumberTo = null;

    public function getId(): ?int
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    #[ORM\ManyToMany(targetEntity: Operation::class, mappedBy: 'operationCategories', cascade: ['persist'])]
    private Collection $operations;

    public function __construct()
    {
        $this->operations = new ArrayCollection();
    }

    /**
     * @return Collection<int, Operation>
     */
    public function getOperation(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): static
    {
        if (!$this->operations->contains($operation)) {
            $this->operations->add($operation);
            $operation->addOperationCategory($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): static
    {
        if ($this->operations->removeElement($operation)) {
            $operation->removeOperationCategory($this);
        }

        return $this;
    }



    public function getOperationNumberFrom(): ?int
    {
        return $this->operationNumberFrom;
    }

    public function setOperationNumberFrom(?int $operationNumberFrom): self
    {
        $this->operationNumberFrom = $operationNumberFrom;

        return $this;
    }

    public function getOperationNumberTo(): ?int
    {
        return $this->operationNumberTo;
    }

    public function setOperationNumberTo(?int $operationNumberTo): self
    {
        $this->operationNumberTo = $operationNumberTo;

        return $this;
    }

}
