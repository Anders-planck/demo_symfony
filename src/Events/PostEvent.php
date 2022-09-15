<?php

namespace App\Events;

use App\Entity\Post;
use Symfony\Contracts\EventDispatcher\Event;

class PostEvent extends Event
{
    /**
     * @var Post
     */
    private Post $post;


    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     * @return PostEvent
     */
    public function setPost(Post $post): PostEvent
    {
        $this->post = $post;
        return $this;
    }

}