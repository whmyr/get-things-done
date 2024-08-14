<?php

declare(strict_types=1);

namespace Whmyr\Taskmanager\Command;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Context\Context;
use Whmyr\Taskmanager\Event\SendReminderMailEvent;
use Whmyr\Taskmanager\Service\TaskService;

#[AsCommand(
    name: 'whmyr:taskmanager:send-reminder-mails',
    description: 'Send mails with reminder date set to actual current date and time',
)]
class SendReminderMailsCommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly TaskService $taskService,
        private readonly Context $context,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tasks = $this->taskService->getTasksWithDueReminderMail(
            $this->context->getPropertyFromAspect('date', 'full'),
        );

        foreach ($tasks as $task) {

            $email = null;

            if ($task->getOwner()->getEmail() !== '') {
                $email = $task->getOwner()->getEmail();
            }

            if ($task->getAssignee() !== null && $task->getAssignee()->getEmail() !== '') {
                $email = $task->getAssignee()->getEmail();
            }

            if ($email === null) {
                $this->logger->warning('Task with uid ' . $task->getUid() . ' has no assignee or owner email address');
                continue;
            }

            $this->eventDispatcher->dispatch(new SendReminderMailEvent($email, $task));
        }

        $output->writeln('Finished');
        return Command::SUCCESS;
    }
}
