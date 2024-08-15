<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'Task',
        'label' => 'title',
        'delete' => 'deleted',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'columns' => [
        'title' => [
            'label' => 'Title',
            'config' => [
                'type' => 'input',
                'required' => true,
            ],
        ],
        'description' => [
            'label' => 'Description',
            'config' => [
                'type' => 'text',
                'required' => true,
            ],
        ],
        'done' => [
            'label' => 'Done',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'due_date' => [
            'label' => 'Due Date',
            'config' => [
                'type' => 'datetime',
            ],
        ],
        'reminder_date' => [
            'label' => 'Reminder date',
            'config' => [
                'type' => 'datetime',
            ],
        ],
        'reminder_sent_date' => [
            'label' => 'Reminder sent date',
            'config' => [
                'type' => 'datetime',
            ],
        ],
        'owner' => [
            'label' => 'Owner',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
            ],
        ],
        'assigned_to' => [
            'label' => 'Assigned To',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'items' => [
                    [
                        'label' => 'Please choose',
                        'value' => 0,
                    ],
                ],
            ],
        ],
        'task_category' => [
            'label' => 'Task category',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_category',
                'items' => [
                    [
                        'label' => 'Please choose',
                        'value' => 0,
                    ],
                ],
            ],
        ],
        'crdate' => [
            'label' => 'Creation date',
            'config' => [
                'type' => 'datetime',
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'title, description, done, task_category, owner, assigned_to, due_date, reminder_date',
        ],
    ],
];
