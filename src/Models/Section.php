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
#[Table(name: 'sections')]
class Section
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $int;

    #[Column(type: 'integer')]
    private int $course_id;

    #[Column(type: 'string')]
    private string $title;

    #[Column(type: 'text')]
    private string $description;

    #[Column(type: 'datetime')]
    private DateTime $created_at;

    #[Column(type: 'datetime', nullable: true)]
    private DateTime $updated_at;

    #[Column(type: 'boolean')]
    private bool $deleted;

    #[ManyToOne(targetEntity: Course::class, inversedBy: 'sections')]
    #[JoinColumn(name: 'course_id', referencedColumnName: 'id')]
    private Course|null $course = null;

    #[OneToMany(targetEntity: Step::class, mappedBy: 'section')]
    private Collection $steps;

    public function __construct()
    {
        $this->steps = new ArrayCollection();
        $this->deleted = false;
    }

    public function getInt(): int
    {
        return $this->int;
    }

    public function setInt(int $id): void
    {
        $this->int = $id;
    }

    public function getCourseId(): int
    {
        return $this->course_id;
    }

    public function setCourseId(int $course_id): void
    {
        $this->course_id = $course_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
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

    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getCourse(): Course|null
    {
        return $this->course;
    }

    public function setCourse(Course $course): void
    {
        $this->course = $course;
    }

    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function setSteps(Collection $steps): void
    {
        $this->steps = $steps;
    }
}