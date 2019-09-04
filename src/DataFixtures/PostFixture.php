<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PostFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $post = new Post();
        $post->setUser($this->getReference('user'));
        $post->setTitle('post title');
        $post->setContent('post content');
        $post->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($post);
        $manager->flush();

        $this->addReference('post', $post);
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
