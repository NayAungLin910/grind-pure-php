<?php

namespace Src\Models;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

#[Entity]
#[Table(name: 'chocies')]
class Choice
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'integer')]
    private int $assignment_id;

    #[Column(type: 'integer')]
    private int $question_id;

    #[Column(type: 'integer')]
    private int $answer_id;

    #[Column(type: 'datetime')]
    private DateTime $created_at;

    #[Column(type: 'datetime', nullable: true)]
    private DateTime $updated_at;

    #[ManyToOne(targetEntity: Assignment::class, inversedBy: 'choices')]
    #[JoinColumn(name: 'assignment_id', referencedColumnName: 'id')]
    private Assignment|null $assignment;

    public function __construct() {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getAssignmentId(): int
    {
        return $this->assignment_id;
    }

    public function setAssignmentId(int $assignment_id): void
    {
        $this->assignment_id = $assignment_id;
    }

    public function getQuestionId(): int
    {
        return $this->question_id;
    }

    public function setQuestionId(int $question_id): void
    {
        $this->question_id = $question_id;
    }

    public function getAnswerId(): int
    {
        return $this->answer_id;
    }

    public function setAnswerId(int $answer_id): void
    {
        $this->answer_id = $answer_id;
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

    public function getAssignment(): Assignment|null
    {
        return $this->assignment;
    }

    public function setAssignment(Assignment $assignment): void
    {
        $this->assignment = $assignment;
    }
}