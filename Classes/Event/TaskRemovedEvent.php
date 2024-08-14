<?php

declare(strict_types=1);

namespace Whmyr\Taskmanager\Event;

use Whmyr\Taskmanager\Domain\Model\Task;

final class TaskRemovedEvent
{
    public function __construct(
        private readonly Task $task,
    ) {}

    public function getTask(): Task
    {
        return $this->task;
    }
}
