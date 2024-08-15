<?php

declare(strict_types=1);

namespace Whmyr\Taskmanager\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Pagination\SlidingWindowPagination;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use Whmyr\Taskmanager\Domain\Model\Task;
use Whmyr\Taskmanager\Exception\NotAllowedToEditTaskException;
use Whmyr\Taskmanager\Service\CategoryService;
use Whmyr\Taskmanager\Service\TaskService;

class TaskController extends ActionController
{
    private UserAspect $userAspect;

    public function __construct(
        private readonly TaskService $taskService,
        private readonly CategoryService $categoryService,
        private readonly CacheManager $cacheManager,
        Context $context,
    ) {
        $this->userAspect = $context->getAspect('frontend.user');
    }

    public function initializeAction(): void
    {
        if (! str_starts_with($this->actionMethodName, 'persist')) {
            return;
        }

        if (! $this->arguments->hasArgument('task')) {
            return;
        }

        $this->configureDateTimeConverterForProperty('dueDate');
        $this->configureDateTimeConverterForProperty('reminderDate');
    }

    public function listAction(): ResponseInterface
    {
        if (! $this->userAspect->isLoggedIn()) {
            return new ForwardResponse('unauthorized');
        }

        $currentPage = 1;
        if ($this->request->hasArgument('currentPage')) {
            $currentPage = (int)$this->request->getArgument('currentPage');
        }

        $taskCreateFormPageUid = $this->settings['taskCreateFormPageUid'] ?: null;
        $itemsPerPage = (int)$this->settings['itemsPerPage'] ?: 10;
        $maxNumberOfLinks = (int)$this->settings['maxNumberOfLinks'] ?: 5;

        $tasks = $this->taskService->getAllTasks();

        $paginator = new QueryResultPaginator($tasks, $currentPage, $itemsPerPage);
        $pagination = new SlidingWindowPagination($paginator, $maxNumberOfLinks);

        $this->view->assign('pagination', $pagination);
        $this->view->assign('createFormUid', $taskCreateFormPageUid);

        return $this->htmlResponse();
    }

    public function createAction(?Task $task = null): ResponseInterface
    {
        if (! $this->userAspect->isLoggedIn()) {
            return new ForwardResponse('unauthorized');
        }

        if ($task !== null) {
            $this->view->assign('task', $task);
        }
        $this->view->assign('categories', $this->categoryService->getAllCategories());

        return $this->htmlResponse();
    }

    public function persistNewTaskAction(Task $task): ResponseInterface
    {
        if (! $this->userAspect->isLoggedIn()) {
            return new ForwardResponse('unauthorized');
        }

        $this->taskService->createTask($task, $this->userAspect->get('id'));

        $this->addFlashMessage(
            messageBody: 'Successfully created new task with title: ' . $task->getTitle(),
            messageTitle: 'Task created!',
        );
        $this->flushPageCache();

        return $this->redirect('updated');
    }

    public function editAction(Task $task): ResponseInterface
    {
        if (! $this->userAspect->isLoggedIn()) {
            return new ForwardResponse('unauthorized');
        }
        if ((int)$this->userAspect->get('id') !== $task->getOwner()->getUid()) {
            return new ForwardResponse('forbidden');
        }

        $this->view->assign('task', $task);
        $this->view->assign('categories', $this->categoryService->getAllCategories());

        return $this->htmlResponse();
    }

    public function persistExistingTaskAction(Task $task): ResponseInterface
    {
        if (! $this->userAspect->isLoggedIn()) {
            return new ForwardResponse('unauthorized');
        }

        try {
            $this->taskService->updateTask($task, $this->userAspect->get('id'));
            $this->addFlashMessage(
                messageBody: 'Successfully updated task with title: ' . $task->getTitle(),
                messageTitle: 'Task updated!',
            );
        } catch (NotAllowedToEditTaskException) {
            return new ForwardResponse('forbidden');
        }

        return new ForwardResponse('updated');
    }

    public function completeAction(Task $task): ResponseInterface
    {
        if (! $this->userAspect->isLoggedIn()) {
            return new ForwardResponse('unauthorized');
        }

        try {
            $this->taskService->completeTask($task, (int)$this->userAspect->get('id'));
            $this->addFlashMessage(
                messageBody: 'Successfully completed task with title: ' . $task->getTitle(),
                messageTitle: 'Task completed!',
            );
            $this->flushPageCache();
        } catch (NotAllowedToEditTaskException) {
            return new ForwardResponse('forbidden');
        }

        return new ForwardResponse('updated');
    }

    public function markUndoneAction(Task $task): ResponseInterface
    {
        if (! $this->userAspect->isLoggedIn()) {
            return new ForwardResponse('unauthorized');
        }

        try {
            $this->taskService->markAsUndone($task, (int)$this->userAspect->get('id'));
            $this->addFlashMessage(
                messageBody: 'Successfully set state to undone for task "' . $task->getTitle() . '"',
                messageTitle: 'Task set to undone!',
            );
            $this->flushPageCache();
        } catch (NotAllowedToEditTaskException) {
            return new ForwardResponse('forbidden');
        }

        return new ForwardResponse('updated');
    }

    public function deleteAction(Task $task): ResponseInterface
    {
        if (! $this->userAspect->isLoggedIn()) {
            return new ForwardResponse('unauthorized');
        }

        try {
            $title = $task->getTitle();
            $this->taskService->deleteTask($task, (int)$this->userAspect->get('id'));

            $this->addFlashMessage(
                messageBody: 'Successfully removed task with title: ' . $title,
                messageTitle: 'Task deleted!',
            );
            $this->flushPageCache();
        } catch (NotAllowedToEditTaskException) {
            return new ForwardResponse('forbidden');
        }

        return $this->redirect('updated');
    }

    /**
     * Intermediate action rendering the list view in an uncached variant to show
     * status flashmessage without making the list view uncacheable completely
     */
    public function updatedAction(): ResponseInterface
    {
        return new ForwardResponse('list');
    }

    public function unauthorizedAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    public function forbiddenAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    public function flushPageCache(): void
    {
        /** @var TypoScriptFrontendController $frontendController */
        $frontendController = $this->request->getAttribute('frontend.controller');
        $this->cacheManager->flushCachesByTag('pageId_' . $frontendController->id);
    }

    public function configureDateTimeConverterForProperty(string $propertyName): void
    {
        $this->arguments['task']
            ->getPropertyMappingConfiguration()
            ->forProperty($propertyName)
            ->setTypeConverterOption(
                DateTimeConverter::class,
                DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'Y-m-d\TH:i',
            );
    }
}
