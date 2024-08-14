<?php

declare(strict_types=1);

namespace Whmyr\Taskmanager\EventListener;

use TYPO3\CMS\Core\Database\ConnectionPool;
use Whmyr\Taskmanager\Domain\Repository\TaskRepository;
use Whmyr\Taskmanager\Event\TaskUpdatedEvent;

final class ResetReminderSentState
{
    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {}

    public function __invoke(TaskUpdatedEvent $event): void
    {
        $updatedTask = $event->getUpdatedTask();

        if (! $updatedTask->_isDirty('reminderDate')) {
            return;
        }

        /*
            @TODO (possible improvement)
            update task object directly when TYPO3 is updated to a version containing this patch:
            https://forge.typo3.org/issues/103641

            Currently TYPO3 is not able to set a null value to a \DateTime domain object property
            on a persistent object even when the property is declared as nullable.

            That's why $updatedTask->setReminderSentDate(null) does not work yet.
        */

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(TaskRepository::TASK_TABLE);

        $queryBuilder
            ->update(TaskRepository::TASK_TABLE)
            ->set('reminder_sent_date', '0')
            ->where(
                $queryBuilder->expr()->eq('uid', $updatedTask->getUid())
            )
            ->executeStatement();
    }
}
