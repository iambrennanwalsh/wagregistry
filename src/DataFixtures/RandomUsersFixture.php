<?php

namespace App\DataFixtures;

use App\Entity\SupportMessage;
use App\Entity\SupportTicket;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RandomUsersFixture extends Fixture
{
  public function load(ObjectManager $manager): void
  {
    $faker = Factory::create();
    for ($user = 0; $user < 10; $user++) {
      $randomUser = new User(['name' => "Test User $user", 'email' => "testuser$user@wagregistry.com", 'password' => 'shirehobbit']);
      $manager->persist($randomUser);
      for ($ticket = 0; $ticket < 10; $ticket++) {
        $supportTicket = new SupportTicket([
          'subject' => $faker->text(20),
          'user' => $randomUser
        ]);
        $manager->persist($supportTicket);
        for ($message = 0; $message < 20; $message++) {
          $supportMessage = new SupportMessage([
            'message' => $faker->text(),
            'admin' => $faker->boolean(),
            'ticket' => $supportTicket
          ]);
          $manager->persist($supportMessage);
        }
      }
    }
    $manager->flush();
  }
}
