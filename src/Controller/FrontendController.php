<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

#[Route(name: "frontend.")]
class FrontendController extends InertiaController
{
  #[Route('/', name: 'home')]
  public function home()
  {
    $this->addFlash('info', "testing");
    return $this->inertia('frontend.home');
  }

  #[Route('/about', name: 'about')]
  public function about()
  {
    return $this->inertia('frontend.about');
  }
}
