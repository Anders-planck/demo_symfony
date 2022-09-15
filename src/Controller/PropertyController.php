<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
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
    #[Route('/property', name: 'property.index')]
    public function index(): Response
    {
        $posts = $this->postRepository->findAllPosted(true);
        return $this->render('property/index.html.twig', [
            'controller_name' => 'PropertyController',
            'current_menu' => 'property',
            'posts' => $posts
        ]);
    }

    /**
     * @param Post $post
     * @param string $slug
     * @return Response
     */
    #[Route('/property/{slug}-{id}', name: 'property.show',requirements: ["slug" =>"[a-z0-9\-]*"])]
    public  function show(Post $post, string $slug):Response
    {
        if ($post->getSlug() !== $slug){
            return $this->redirectToRoute('property.show',[
                'id' => $post->getId(),
                'slug' => $post->getSlug()
            ],301);
        }
        return $this->render('property/show.html.twig', [
            'controller_name' => 'PropertyController',
            'current_menu' => 'property',
            'post' => $post
        ]);
    }
}
