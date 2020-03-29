<?php

namespace App\EventSubscriber;

use App\Event\PostPreCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
        $slug = base64_encode(random_bytes(32));
        $slug = str_replace(['+', '/', '='], '-', mb_substr($slug, 0, 16));

        $post = $event->getPost();
        $post->setSlug($slug);
    }
}
