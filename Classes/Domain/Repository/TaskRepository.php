<?php

declare(strict_types=1);

namespace Whmyr\Taskmanager\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\Repository;

class TaskRepository extends Repository
{
    public const TASK_TABLE = 'tx_getthingsdone_domain_model_task';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {
        parent::__construct();
    }

    public function findWithDueReminderDate(\DateTimeImmutable $currentDate): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TASK_TABLE);

        return $queryBuilder
            ->select('*')
            ->from(self::TASK_TABLE, 'task')
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->lte('reminder_date', $currentDate->getTimestamp()),
                    $queryBuilder->expr()->neq('reminder_date', 0),
                    $queryBuilder->expr()->eq('reminder_sent_date', 0),
                    $queryBuilder->expr()->eq('done', 0),
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
