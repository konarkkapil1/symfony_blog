<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PostController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(PostRepository $postRepo): Response
    {
        $posts = $postRepo->findAll();
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/post/create", name="create_post")
     * @param Request
     * @return Response
     */
    public function create(Request $req){
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);




        $form->handleRequest($req);

        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            /**
             * @var UploadedFile $file
             */
            $file = $req->files->get('post')['image'];
            if($file){
                $filename = md5(uniqid()). '.' .$file->guessClientExtension();

                $file->move(
                    $this->getParameter('uploads_dir'),
                    $filename
                );
                $post->setImage($filename);
            }

            $em->persist($post);
            $em->flush();
            return $this->redirect($this->generateUrl('home'));
        }



        return $this->render('post/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/post/{id}", name="post")
     * @param Post
     */
    public function show(Post $post): Response{
        return $this->render('post/post.html.twig', ["post" => $post]);
    }


}
