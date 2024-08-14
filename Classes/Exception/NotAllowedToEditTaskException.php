<?php

declare(strict_types=1);

namespace Whmyr\Taskmanager\Exception;

use Whmyr\Taskmanager\Domain\Model\Task;

final class NotAllowedToEditTaskException extends \RuntimeException
{
    public function __construct(
        private readonly Task $task,
        string $message = '',
    ) {
        parent::__construct($message, 1723640418);
    }

    public function getTask(): Task
    {
        return $this->task;
    }
}
