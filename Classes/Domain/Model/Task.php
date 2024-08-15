<?php

declare(strict_types=1);

namespace Whmyr\Taskmanager\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Task extends AbstractEntity
{
    #[Validate([
        'validator' => 'NotEmpty',
    ])]
    protected string $title;

    #[Validate([
        'validator' => 'StringLength',
        'options' => ['minimum' => 10, 'maximum' => 300],
    ])]
    protected string $description;

    protected bool $done;

    protected User $owner;

    protected ?User $assignee = null;

    protected ?Category $category = null;

    protected \DateTime $createdAt;

    protected ?\DateTime $dueDate = null;

    protected ?\DateTime $reminderDate = null;

    protected ?\DateTime $reminderMailSentAt = null;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Task
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Task
    {
        $this->description = $description;
        return $this;
    }

    public function isDone(): bool
    {
        return $this->done;
    }

    public function setDone(bool $done): Task
    {
        $this->done = $done;
        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): Task
    {
        $this->owner = $owner;
        return $this;
    }

    public function getAssignee(): ?User
    {
        return $this->assignee;
    }

    public function setAssignee(User $assignee): Task
    {
        $this->assignee = $assignee;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): Task
    {
        $this->category = $category;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTime $dueDate = null): Task
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getReminderDate(): ?\DateTime
    {
        return $this->reminderDate;
    }

    public function setReminderDate(?\DateTime $reminderDate): Task
    {
        $this->reminderDate = $reminderDate;
        return $this;
    }

    public function getReminderMailSentAt(): ?\DateTime
    {
        return $this->reminderMailSentAt;
    }

    public function setReminderMailSentAt(?\DateTime $reminderMailSentAt): Task
    {
        $this->reminderMailSentAt = $reminderMailSentAt;
        return $this;
    }
}
