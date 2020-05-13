<?php

namespace App\EventSubscriber;

use App\Event\PostPreCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Uid\Uuid;

class PostSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            PostPreCreatedEvent::class => 'onPostPreCreatedEvent',
        ];
    }

    public function onPostPreCreatedEvent(PostPreCreatedEvent $event)
    {
        $post = $event->getPost();
        $post->setSlug((Uuid::v1())->toBase58());
    }
}
