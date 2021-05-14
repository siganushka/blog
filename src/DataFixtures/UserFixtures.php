<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $user->setUsername('acme_foo');
        $user->setNickname('AcmeFoo');
        $user->setAvatar('http://placehold.it/320x320');

        $manager->persist($user);
        $manager->flush();

        $this->addReference('user', $user);
    }
}
