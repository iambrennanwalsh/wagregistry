<?php

namespace App\Controller;

use App\Event\InertiaEvent;
use Rompetomp\InertiaBundle\Service\InertiaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class InertiaController extends AbstractController
{

  protected InertiaInterface $inertia;
  protected EventDispatcherInterface $dispatcher;

  public function __construct(
    InertiaInterface $inertia,
    EventDispatcherInterface $eventDispatcherInterface
  ) {
    $this->inertia = $inertia;
    $this->dispatcher = $eventDispatcherInterface;
  }

  protected function inertia(
    string $component,
    array $props = [],
    array $viewData = [],
    array $context = []
  ): Response {
    $currentUser = $this->getUser();

    $event = new InertiaEvent($currentUser);
    $this->dispatcher->dispatch($event, InertiaEvent::RENDER);

    return $this->inertia->render($component, $props, $viewData, $context);
  }
}
