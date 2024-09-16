<?php

namespace App\Notification;

use App\Notification\FlashMessageImportanceMapper;
use App\Notification\Notification;

class GenericFlashMessageNotification extends Notification
{
  public function __construct(
    string $message,
    string $importance = FlashMessageImportanceMapper::INFO
  ) {
    $this->subject($message);
    $this->importance($importance);
    $this->channels(['browser']);
  }
}
