<?php

namespace App\Event;

use App\Entity\Post;
use Symfony\Contracts\EventDispatcher\Event;

class PostPreCreatedEvent extends Event
{
    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function getPost()
    {
        return $this->post;
    }
}
