<?php

namespace Src\Models;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\OneToMany;

#[Entity]
#[Table(name: 'users')]
class User
{
    const USER_ROLE = 'user';
    const ADMIN_ROLE = 'admin';

    private $roles = [self::USER_ROLE, self::ADMIN_ROLE];

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'string')]
    private string $name;

    #[Column(type: 'string')]
    private string $email;

    #[Column(type: 'string')]
    private string $password;

    #[Column(type: 'string')]
    private string $profile_image;

    #[Column(type: 'string')]
    private string $role;

    #[Column(type: 'datetime')]
    private DateTime $created_at;

    #[Column(type: 'datetime', nullable: true)]
    private DateTime $updated_at;

    #[OneToMany(targetEntity: Course::class, mappedBy: 'user')]
    private Collection $courses;

    #[ManyToMany(targetEntity: Certificate::class, mappedBy: 'users')]
    private Collection $certificates;

    #[ManyToMany(targetEntity: Course::class, inversedBy: 'enrolledCourses')]
    #[JoinTable(name: 'users_courses')]
    private Collection $enrolledCourses;

    #[ManyToMany(targetEntity: Step::class, inversedBy: 'users')]
    #[JoinTable(name: 'users_steps')]
    private Collection $completedSteps;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->certificates = new ArrayCollection();
        $this->enrolledCourses = new ArrayCollection();
        $this->completedSteps = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getProfileImage(): string
    {
        return $this->profile_image;
    }

    public function setProfileImage(string $profile_image): void
    {
        $this->profile_image = $profile_image;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole($role): void
    {
        if (!in_array($role, $this->roles)) {
            throw new \InvalidArgumentException("The assigned role is invalid.");
        }

        $this->role = $role;
    }

    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function setCourses(Collection $courses): void
    {
        $this->courses = $courses;
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

    public function getCertificates(): Collection
    {
        return $this->certificates;
    }

    public function setCertificates(Collection $certificates): void
    {
        $this->certificates = $certificates;
    }

    public function getEnrolledCourses(): Collection
    {
        return $this->enrolledCourses;
    }

    public function setEnrolledCourses(Collection $enrolledCourses): void
    {
        $this->enrolledCourses = $enrolledCourses;
    }

    public function getCompletedSteps(): Collection
    {
        return $this->completedSteps;
    }

    public function setCompletedSteps(Collection $completedSteps): void
    {
        $this->completedSteps = $completedSteps;
    }
}
