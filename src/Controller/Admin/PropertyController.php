<?php
namespace App\Controller\Admin;

use App\Entity\Post;
use App\Events\PostEvent;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController{

    /**
     * @var PostRepository
     */
    private PostRepository $postRepository;

    /**
     * @var ObjectManager
     */
    private ObjectManager $em;

    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param PostRepository $postRepository
     * @param ManagerRegistry $doctrine
     */
    public function __construct(PostRepository $postRepository, ManagerRegistry $doctrine,EventDispatcherInterface $eventDispatcher)
    {
        $this->postRepository = $postRepository;
        $this->em =$doctrine->getManager();
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return Response
     */
    #[Route('/admin/property',name: 'admin.property.index')]
    public function index():Response {
        $posts = $this->postRepository->findAll();
        return $this->render("admin/property/index.html.twig",[
            'current_menu' => 'Admin',
            'posts' => $posts
        ]);
    }


    /**
     * @param Post $post
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/property/edit-{id}',name: 'admin.property.edit')]
    public function edit(Post $post,Request $request):Response {
        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->eventDispatcher->dispatch(new PostEvent($post),"post.pre_update");
            $this->em->flush();
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render("admin/property/edit.html.twig",[
            'current_menu' => 'Admin',
            'post' => $post,
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/property/new',name: 'admin.property.new')]
    public function new(Request $request):Response{
        $post = new Post();
        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $post = $form->getData();
            $this->em->persist($post);
            $this->em->flush();
            return $this->redirectToRoute("admin.property.index");
        }

        return $this->render("admin/property/new.html.twig",[
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Post $post
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/property/{id}',name: 'admin.property.delete',methods: ['POST','DELETE'])]
    public function delete(Post $post,Request $request):Response {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->get('_token'))){
            $this->eventDispatcher->dispatch(new PostEvent($post),'post.pre_remove');
            $this->em->remove($post);
            $this->em->flush();
        }
        return $this->redirectToRoute('admin.property.index');
    }
}