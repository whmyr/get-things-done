<?php

declare(strict_types=1);

namespace Whmyr\Taskmanager\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Property\TypeConverter\ArrayConverter;
use Whmyr\Taskmanager\Service\TaskService;

class AsyncTaskController extends ActionController
{
    private UserAspect $userAspect;

    public function __construct(
        private readonly TaskService $taskService,
        Context $context
    ) {
        $this->userAspect = $context->getAspect('frontend.user');
    }

    public function initializeGetCurrentUserPermissionsForTaskListAction(): void
    {
        $this->arguments['taskList']->getPropertyMappingConfiguration()->setTypeConverterOption(
            ArrayConverter::class,
            ArrayConverter::CONFIGURATION_DELIMITER,
            ','
        );
    }

    public function getCurrentUserPermissionsForTaskListAction(array $taskList): ResponseInterface
    {
        $result = [];
        $userId = $this->userAspect->get('id');

        foreach ($taskList as $taskUid) {

            $task = $this->taskService->getTaskById((int)$taskUid);

            $result[] = [
                'uid' => $task->getUid(),
                'toggleState' => $this->taskService->isAllowedToModifyTask($task, $userId, 'completeTask'),
                'modify' => $this->taskService->isAllowedToModifyTask($task, $userId, 'update'),
            ];
        }

        return new JsonResponse($result);
    }
}
