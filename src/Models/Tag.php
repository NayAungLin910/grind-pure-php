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
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'tags')]
class Tag
{

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'integer')]
    private int $user_id;

    #[Column(type: 'string')]
    private string $name;

    #[Column(type: 'datetime')]
    private DateTime $created_at;

    #[Column(type: 'datetime', nullable: true)]
    private DateTime $updated_at;

    #[Column(type: 'boolean')]
    private bool $deleted;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'tags')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[ManyToMany(targetEntity: Course::class, inversedBy: 'tags')]
    #[JoinTable(name: 'courses_tags')]
    private Collection $courses;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->created_at = new DateTime();
        $this->deleted = false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): DateTime
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function getUndeletedCourses(): Collection
    {
        $unDeletedCourses = $this->courses->filter(function ($c) { // on get undeleted sections
            return $c->getDeleted() === false;
        });
        return $unDeletedCourses;
    }

    public function setCourses(Collection $courses): void
    {
        $this->courses = $courses;
    }
}
