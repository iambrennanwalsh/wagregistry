<?php

namespace App\Notification;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification as SymfonyNotification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class Notification extends SymfonyNotification implements
  EmailNotificationInterface
{
  private string $emailSubject = "";
  private string $template = "";
  private array $context = [];
  private array $files = [];

  public function emailSubject(string $emailSubject): static
  {
    $this->emailSubject = $emailSubject;
    return $this;
  }

  public function getEmailSubject(): string
  {
    return $this->emailSubject;
  }

  public function template(string $template): static
  {
    $this->template = "/email$template.twig";
    return $this;
  }

  public function getTemplate(): string
  {
    return $this->template;
  }

  public function context(array $context): static
  {
    $this->context = $context;
    return $this;
  }

  public function getContext(): array
  {
    return $this->context;
  }

  public function files(string $path, ?string $name): static
  {
    array_push($this->files, [
      'path' => $path,
      'name' => $name
    ]);
    return $this;
  }

  public function getFiles(): array
  {
    return $this->files;
  }

  public function asEmailMessage(
    EmailRecipientInterface $recipient,
    string $transport = null
  ): EmailMessage {
    $subject = $this->getEmailSubject();

    if ($subject == "") {
      $subject = $this->getSubject();
    }

    $email = (new TemplatedEmail())
      ->to($recipient->getEmail())
      ->subject($subject)
      ->htmlTemplate($this->getTemplate())
      ->context($this->getContext());

    $files = $this->getFiles();

    if (!empty($files)) {
      foreach ($files as $file) {
        $part = new DataPart(new File($file['path']));
        if ($name = $file['name']) {
          $part->setName($name);
        }
        $email->addPart($part);
      }
    }

    return new EmailMessage($email);
  }
}
