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
#[Table(name: 'assignments')]
class Assignment
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'integer')]
    private int $step_id;

    #[Column(type: 'datetime')]
    private DateTime $start_time;

    #[Column(type: 'datetime')]
    private DateTime $created_at;

    #[Column(type: 'datetime', nullable: true)]
    private DateTime $updated_at;
    
    #[ManyToOne(targetEntity: Step::class, inversedBy: 'assignments')]
    #[JoinColumn('step_id', referencedColumnName: 'id')]
    private Step|null $step;

    #[OneToMany(targetEntity: Choice::class, mappedBy: 'assignemnt')]
    private Collection $choices;

    public function __construct()
    {
        $this->choices = new ArrayCollection();
    }

    // Getters and Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getStepId(): int
    {
        return $this->step_id;
    }

    public function setStepId(int $step_id): void
    {
        $this->step_id = $step_id;
    }

    public function getStartTime(): DateTime
    {
        return $this->start_time;
    }

    public function setStartTime(DateTime $start_time): void
    {
        $this->start_time = $start_time;
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

    public function getChoices(): Collection
    {
        return $this->choices;
    }

    public function setChoices(Collection $choices): void
    {
        $this->choices = $choices;
    }
}