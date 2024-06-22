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
use InvalidArgumentException;

#[Entity]
#[Table(name: 'steps')]
class Step
{
    const SECTION_READING = 'reading';
    const SECTION_QUIZ = 'quiz';
    const SECTION_VIDEO = 'video';

    const sectionTypes = [
        self::SECTION_READING,
        self::SECTION_QUIZ,
        self::SECTION_VIDEO
    ];

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'integer')]
    private int $section_id;

    #[Column(type: 'string', nullable: true)]
    private string $type;

    #[Column(type: 'text')]
    private string $title;

    #[Column(type: 'text')]
    private string $video;

    #[Column(type: 'text')]
    private string $description;

    #[Column(type: 'text')]
    private string $reading_content;

    #[Column(type: 'integer')]
    private int $time_given;

    #[Column(type: 'datetime')]
    private DateTime $created_at;

    #[Column(type: 'datetime', nullable: true)]
    private DateTime $updated_at;

    #[Column(type: 'boolean')]
    private bool $deleted;

    #[Column(type: 'integer')]
    private int $priority;

    #[ManyToOne(targetEntity: Section::class, inversedBy: 'steps')]
    #[JoinColumn(name: 'section_id', referencedColumnName: 'id')]
    private Section|null $section = null;

    #[ManyToMany(targetEntity: User::class, inversedBy: 'completedSteps')]
    #[JoinTable(name: 'users_steps')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->type = static::SECTION_READING;
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

    public function getSectionId(): int
    {
        return $this->section_id;
    }

    public function setSectionId(int $section_id): void
    {
        $this->section_id = $section_id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        if (!in_array($type, self::sectionTypes)) {
            throw new InvalidArgumentException("Invalid section type.");
        }

        $this->type = $type;
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

    public function getVideo(): string
    {
        return $this->video;
    }

    public function setVideo(string $video): void
    {
        $this->video = $video;
    }

    public function getReadingContent(): string
    {
        return $this->reading_content;
    }

    public function setReadingContent(string $reading_content): void
    {
        $this->reading_content = $reading_content;
    }

    public function getTimeGiven(): int
    {
        return $this->time_given;
    }

    public function setTimeGiven(int $time_given): void
    {
        $this->time_given = $time_given;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getSection(): Section|null
    {
        return $this->section;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function setSection(Section $section): void
    {
        $this->section = $section;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setUsers(Collection $users): void
    {
        $this->users = $users;
    }
}
