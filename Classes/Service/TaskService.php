<?php

declare(strict_types=1);

namespace Whmyr\Taskmanager\Service;

use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use Whmyr\Taskmanager\Domain\Model\Task;
use Whmyr\Taskmanager\Domain\Repository\TaskRepository;
use Whmyr\Taskmanager\Domain\Repository\UserRepository;
use Whmyr\Taskmanager\Event\TaskCreatedEvent;
use Whmyr\Taskmanager\Event\TaskRemovedEvent;
use Whmyr\Taskmanager\Event\TaskUpdatedEvent;
use Whmyr\Taskmanager\Exception\NotAllowedToEditTaskException;

class TaskService implements SingletonInterface
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly UserRepository $userRepository,
        private readonly DataMapper $dataMapper,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    public function getAllTasks(): QueryResultInterface
    {
        return $this->taskRepository->findAll();
    }

    /**
     * @return array<int,Task>
     */
    public function getTasksWithDueReminderMail(\DateTimeImmutable $currentDate): array
    {
        $tasksArray = $this->taskRepository->findWithDueReminderDate($currentDate);

        return $this->dataMapper->map(Task::class, $tasksArray);
    }

    public function createTask(Task $task, int $authorUid): void
    {
        $user = $this->userRepository->findOneBy(['uid' => $authorUid]);
        $task->setOwner($user);
        $this->taskRepository->add($task);

        $this->eventDispatcher->dispatch(new TaskCreatedEvent($task));
    }

    public function updateTask(Task $updatedTask, int $userId): void
    {
        if (! $this->isAllowedToModifyTask($updatedTask, $userId, __FUNCTION__)) {
            throw new NotAllowedToEditTaskException(
                $updatedTask,
                'User with uid ' . $userId . ' tried to edit task with uid ' . $updatedTask->getUid(),
            );
        }

        $this->taskRepository->update($updatedTask);

        $this->eventDispatcher->dispatch(new TaskUpdatedEvent($updatedTask));
    }

    public function completeTask(Task $task, int $userId): void
    {
        if (! $this->isAllowedToModifyTask($task, $userId, __FUNCTION__)) {
            throw new NotAllowedToEditTaskException(
                $task,
                'User with uid ' . $userId . ' tried to complete task with uid ' . $task->getUid(),
            );
        }

        $this->setTaskState($task, true);
    }

    public function markAsUndone(Task $task, int $userId): void
    {
        if (! $this->isAllowedToModifyTask($task, $userId, __FUNCTION__)) {
            throw new NotAllowedToEditTaskException(
                $task,
                'User with uid ' . $userId . ' tried to mark task with uid ' . $task->getUid() . ' as undone',
            );
        }

        $this->setTaskState($task, false);
    }

    public function deleteTask(Task $task, int $userId): void
    {
        if (! $this->isAllowedToModifyTask($task, $userId, __FUNCTION__)) {
            throw new NotAllowedToEditTaskException(
                $task,
                'User with uid ' . $userId . ' tried to delete task with uid ' . $task->getUid(),
            );
        }

        $orignalTask = clone $task;

        $this->taskRepository->remove($task);

        $this->eventDispatcher->dispatch(new TaskRemovedEvent($orignalTask));
    }

    public function isAllowedToModifyTask(Task $task, int $userId, string $actionName = ''): bool
    {
        $assignee = $task->getAssignee();
        $owner = $task->getOwner();

        if ($actionName !== 'completeTask') {
            return $owner->getUid() === $userId;
        }

        return $owner->getUid() === $userId || ($assignee && $assignee->getUid() === $userId);
    }

    public function setTaskState(Task $task, bool $isDone): void
    {
        $task->setDone($isDone);
        $this->taskRepository->update($task);

        $this->eventDispatcher->dispatch(new TaskUpdatedEvent($task));
    }
}
