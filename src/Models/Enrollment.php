<?php

namespace Src\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'users_courses')]
class Enrollment
{
    const ENROLLMENT_COMPLETED = 'completed';
    const ENROLLMENT_UNCOMPLETE = 'uncomplete';

    const statusTypes = [
        self::ENROLLMENT_COMPLETED,
        self::ENROLLMENT_UNCOMPLETE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'enrollments')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'enrollers')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id')]
    private ?Course $course = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private string $status;

    #[ORM\Column(type: 'datetime')]
    private DateTime $created_at;

    public function __construct() {
        $this->status = self::ENROLLMENT_UNCOMPLETE;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): void
    {
        $this->course = $course;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        if (!in_array($status, self::statusTypes)) {
            throw new InvalidArgumentException("Invalid status type.");
        }

        $this->status = $status;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }
}
