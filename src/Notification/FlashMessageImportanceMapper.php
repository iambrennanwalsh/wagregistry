<?php

namespace App\Notification;

use Symfony\Component\Notifier\FlashMessage\AbstractFlashMessageImportanceMapper;
use Symfony\Component\Notifier\FlashMessage\FlashMessageImportanceMapperInterface;

class FlashMessageImportanceMapper extends AbstractFlashMessageImportanceMapper implements FlashMessageImportanceMapperInterface
{
  public const string DANGER = 'danger';
  public const string WARNING = 'warning';
  public const string INFO = 'info';
  public const string SUCCESS = 'success';

  protected const IMPORTANCE_MAP = [
    self::DANGER => 'danger',
    self::WARNING => 'warning',
    self::INFO => 'info',
    self::SUCCESS => 'success'
  ];
}
