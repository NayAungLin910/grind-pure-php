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
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'courses')]
class Course
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'string')]
    private string $title;

    #[Column(type: 'text')]
    private string $description;

    #[Column(type: 'string')]
    private string $image;

    #[Column(type: 'integer')]
    private int $user_id;

    #[Column(type: 'datetime')]
    private DateTime $created_at;

    #[Column(type: 'datetime', nullable: true)]
    private DateTime $updated_at;

    #[Column(type: 'boolean')]
    private bool $deleted;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'courses')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User|null $user = null;

    #[OneToMany(targetEntity: Certificate::class, mappedBy: 'course')]
    private Collection $certificates;

    #[ManyToMany(targetEntity: User::class, inversedBy: 'enrolledCourses')]
    #[JoinTable(name: 'users_courses')]
    private Collection $users;

    #[ManyToMany(targetEntity: Tag::class, mappedBy: 'courses')]
    private Collection $tags;

    #[OneToMany(targetEntity: Section::class, mappedBy: 'course')]
    #[OrderBy(["priority" => "ASC"])]
    private Collection $sections;

    public function __construct()
    {
        $this->certificates = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->created_at = new DateTime();
        $this->deleted = false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
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

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): User|null
    {
        return $this->user;
    }

    public function getCertificates(): Collection
    {
        return $this->certificates;
    }

    public function setCertificates(Collection $certificates): void
    {
        $this->certificates = $certificates;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setTags(Collection $tags): void
    {
        $this->tags = $tags;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function getUndeletedTags(): Collection
    {
        $unDeletedTags = $this->tags->filter(function ($t) { // on get undeleted tags
            return $t->getDeleted() == false;
        });
        return $unDeletedTags;
    }

    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function getUndeletedSections(): Collection
    {
        $unDeletedSections = $this->sections->filter(function ($s) { // on get undeleted sections
            return $s->getDeleted() == false;
        });
        return $unDeletedSections;
    }


    public function setSections(Collection $sections): void
    {
        $this->sections = $sections;
    }
}
