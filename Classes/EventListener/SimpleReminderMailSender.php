<?php

namespace Whmyr\Taskmanager\EventListener;

use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use Whmyr\Taskmanager\Domain\Repository\TaskRepository;
use Whmyr\Taskmanager\Event\SendReminderMailEvent;

final class SimpleReminderMailSender
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
        private readonly Context $context,
    ) {}

    public function __invoke(SendReminderMailEvent $event): void
    {
        $recipient = $event->getRecipientEmail();

        $mail = new MailMessage();
        $mail->from(new Address('no-reply@domain.tld', 'Get things done taskmanager'));
        $mail->to(new Address($recipient));
        $mail->subject('Due task: ' . $event->getTask()->getTitle());
        $mail->text('Hi there! The task ' . $event->getTask()->getTitle() . ' has been due. Please have a look!');
        $mail->send();

        $task = $event->getTask();

        /** @var \DateTimeImmutable $currentDateTime */
        $currentDateTime = $this->context->getPropertyFromAspect('date', 'full');

        $task->setReminderMailSentAt(\DateTime::createFromImmutable($currentDateTime));
        $this->taskRepository->update($task);

        $this->persistenceManager->persistAll();
    }
}
