<?php

namespace App\DataFixtures;

use App\Entity\SupportMessage;
use App\Entity\SupportTicket;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TestUserFixture extends Fixture
{
  public function load(ObjectManager $manager): void
  {
    $faker = Factory::create();
    $testUser = new User(['name' => 'Test User', 'email' => 'testuser@wagregistry.com', 'password' => 'shirehobbit']);
    $manager->persist($testUser);
    for ($ticket = 0; $ticket < 10; $ticket++) {
      $supportTicket = new SupportTicket([
        'subject' => $faker->text(20),
        'user' => $testUser
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
    $manager->flush();
  }
}
