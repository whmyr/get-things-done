<?php

declare(strict_types=1);

namespace Whmyr\Taskmanager\Event;

use Whmyr\Taskmanager\Domain\Model\Task;

final class TaskUpdatedEvent
{
    public function __construct(
        private readonly Task $updatedTask,
    ) {}

    public function getUpdatedTask(): Task
    {
        return $this->updatedTask;
    }
}
