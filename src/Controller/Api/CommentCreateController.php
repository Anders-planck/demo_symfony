<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Security;

#[AsController]
class CommentCreateController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(Comment $data):Comment
    {
        $data->setAuthor($this->security->getUser());
        return $data;
    }
}