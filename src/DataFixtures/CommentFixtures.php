<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $comment = new Comment();
        $comment->setUser($this->getReference('user'));
        $comment->setPost($this->getReference('post'));
        $comment->setContent('Comment content');
        $comment->setState(Comment::STATE_APPROVED);

        $manager->persist($comment);
        $manager->flush();

        $this->addReference('comment', $comment);
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            PostFixtures::class,
        ];
    }
}
