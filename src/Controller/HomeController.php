<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @var PostRepository
     */
    private PostRepository $postRepository;

    /**
     * @var ObjectManager
     */
    private ObjectManager $em;


    /**
     * @param PostRepository $postRepository
     * @param ManagerRegistry $doctrine
     */
    public function __construct(PostRepository $postRepository, ManagerRegistry $doctrine)
    {
        $this->postRepository = $postRepository;
        $this->em = $doctrine->getManager();
    }

    /**
     * @return Response
     */
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $posts = $this->postRepository->findAllLasted(2);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'current_menu' => 'home',
            'posts' => $posts
        ]);
    }
}
