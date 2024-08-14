<?php

declare(strict_types=1);

use Whmyr\Taskmanager\Domain\Model\Task;
use Whmyr\Taskmanager\Domain\Model\User;

return [
    Task::class => [
        'tableName' => 'tx_getthingsdone_domain_model_task',
        'recordType' => Task::class,
        'properties' => [
            'createdAt' => [
                'fieldName' => 'crdate',
            ],
            'assignee' => [
                'fieldName' => 'assigned_to',
            ],
            'reminderMailSentAt' => [
                'fieldName' => 'reminder_sent_date',
            ],
        ],
    ],
    User::class => [
        'tableName' => 'fe_users',
        'recordType' => 0,
    ],
];
