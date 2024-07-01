<?php

namespace Src\Models;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'questions')]
class Question
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'integer')]
    private int $step_id;

    #[Column(type: 'text')]
    private string $description;

    #[Column(type: 'datetime')]
    private DateTime $created_at;

    #[Column(type: 'datetime', nullable: true)]
    private DateTime $updated_at;

    #[ManyToOne(targetEntity: Step::class, inversedBy: 'questions')]
    #[JoinColumn(name: 'step_id', referencedColumnName: 'id')]
    private Step|null $step;

    #[OneToMany(targetEntity: Answer::class, mappedBy: 'question')]
    private Collection $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->created_at = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getStepId(): int
    {
        return $this->step_id;
    }

    public function setStepId(int $step_id): void
    {
        $this->step_id = $step_id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(): void
    {
        $this->updated_at = new DateTime();
    }

    public function getStep(): Step|null
    {
        return $this->step;
    }

    public function setStep(Step $step): void
    {
        $this->step = $step;
    }

    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function setAnswers(Collection $answers): void
    {
        $this->answers = $answers;
    }
}
