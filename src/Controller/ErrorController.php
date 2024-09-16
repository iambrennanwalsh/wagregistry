<?php

namespace App\Controller;

use Rompetomp\InertiaBundle\Service\InertiaInterface;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends InertiaController
{

  private ErrorRendererInterface $errorRenderer;

  public function __construct(InertiaInterface $inertia, ErrorRendererInterface $errorRenderer, EventDispatcherInterface $dispatcher)
  {
    $this->errorRenderer = $errorRenderer;
    parent::__construct($inertia, $dispatcher);
  }

  public function error(\Throwable $exception): Response
  {
    $exception = $this->errorRenderer->render($exception);
    return $this->inertia('frontend.error', [
      'statusCode' => $exception->getStatusCode()
    ]);
  }
}
