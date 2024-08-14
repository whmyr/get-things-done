<?php

declare(strict_types=1);

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Whmyr\Taskmanager\Controller\TaskController;

defined('TYPO3') or die();

$cacheableControllerActions = [
    'list',
    'create',
    'unauthorized',
    'forbidden',
];

$unCacheableControllerActions = [
    'edit',
    'complete',
    'markUndone',
    'delete',
    'persistNewTask',
    'persistExistingTask',
    'updated',
];

ExtensionUtility::configurePlugin(
    extensionName: 'GetThingsDone',
    pluginName: 'TaskList',
    controllerActions: [
        TaskController::class => implode(',', array_merge($cacheableControllerActions, $unCacheableControllerActions)),
    ],
    nonCacheableControllerActions: [
        TaskController::class => implode(',', $unCacheableControllerActions),
    ],
);
