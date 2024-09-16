<?php

namespace App\EventListener;

use App\Entity\User;
use App\Event\InertiaEvent;
use Rompetomp\InertiaBundle\Service\InertiaInterface;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[
  AsEventListener(
  event: KernelEvents::REQUEST,
  method: 'onRequest'
)
]
#[
  AsEventListener(
  event: InertiaEvent::RENDER,
  method: 'onInertiaRender'
)
]
class InertiaListener
{

  private InertiaInterface $inertia;
  private ?Request $request;
  public function __construct(
    InertiaInterface $inertia,
    RequestStack $requestStack
  ) {
    $this->inertia = $inertia;
    $this->request = $requestStack->getCurrentRequest();
  }


  public function onRequest(RequestEvent $event)
  {
    $JsonManifest = new JsonManifestVersionStrategy(__DIR__ . '/../../public/build/web/manifest.json');
    $path = $JsonManifest->getVersion('build/web/web.js');
    $version = str_replace('.js', '', str_replace('/build/web/web.', '', $path));
    $this->inertia->version($version);
  }

  private function getNotifications(Request $request)
  {
    $notifications = [
      'info' => [],
      'success' => [],
      'warning' => [],
      'danger' => []
    ];

    if ($request->hasSession()) {
      $session = $request->getSession();
      $bag = $session->getFlashBag();

      $notifications['info'] = $bag->get('info', []);
      $notifications['success'] = $bag->get('success', []);
      $notifications['warning'] = $bag->get('warning', []);
      $notifications['danger'] = $bag->get('danger', []);
    }

    return $notifications;
  }

  private function getTheme(Request $request)
  {
    return $request->cookies->get('theme', 'light');
  }

  private function getAuth(?User $user)
  {
    return [
      'user' => $user !== null ? [
        'id' => $user->getId(),
        'name' => $user->getName(),
        'email' => $user->getEmail(),
        'gravatar' => $user->getGravatar(),
        'emailConfirmation' => $user->getEmailConfirmation()
      ] : null
    ];
  }

  public function onInertiaRender(InertiaEvent $event)
  {
    $notifications = $this->getNotifications($this->request);
    $theme = $this->getTheme($this->request);
    $auth = $this->getAuth($event->getUser());

    $this->inertia->share('notifications', $notifications);
    $this->inertia->share('theme', $theme);
    $this->inertia->share('auth', $auth);
  }
}
