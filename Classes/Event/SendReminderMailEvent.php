<?php

declare(strict_types=1);

namespace Whmyr\Taskmanager\Event;

use Whmyr\Taskmanager\Domain\Model\Task;

final class SendReminderMailEvent
{
    public function __construct(
        private string $recipientEmail,
        private readonly Task $task
    ) {}

    public function getTask(): Task
    {
        return $this->task;
    }

    public function getRecipientEmail(): string
    {
        return $this->recipientEmail;
    }

    public function setRecipientEmail(string $recipientEmail): SendReminderMailEvent
    {
        $this->recipientEmail = $recipientEmail;
        return $this;
    }
}
