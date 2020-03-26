<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Event\PostPreCreatedEvent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function load(ObjectManager $manager)
    {
        $post = new Post();
        $post->setUser($this->getReference('user'));
        $post->setTitle('Post title');
        $post->setContent('Post content');

        $event = new PostPreCreatedEvent($post);
        $this->dispatcher->dispatch($event);

        $manager->persist($post);
        $manager->flush();

        $this->addReference('post', $post);
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
