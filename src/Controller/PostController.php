<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class PostController extends AbstractController
{
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/posts/new", name="app_post_new")
     */
    public function new(Request $request, UserInterface $user)
    {
        $entity = new Post();
        $entity->setUser($user);
        $entity->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm('App\Form\PostType', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('app_index');
        }

        return $this->render('post/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/posts/show/{id}", name="app_post_show")
     */
    public function show(Request $request, $id)
    {
        $entity = $this->postRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException("Posts {$id} is not found.");
        }

        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setState(Comment::STATE_APPROVED);

        $form = $this->createForm('App\Form\CommentType', $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('ROLE_READER');

            $entity->addComment($comment);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $fragment = sprintf('comments-%d', $comment->getId());

            return $this->redirectToRoute('app_post_show', ['id' => $id, '_fragment' => $fragment]);
        }

        return $this->render('post/show.html.twig', [
            'post' => $entity,
            'comment_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/posts/edit/{id}", name="app_post_edit")
     */
    public function edit(Request $request, $id)
    {
        $entity = $this->postRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException("Posts {$id} is not found.");
        }

        $form = $this->createForm('App\Form\PostType', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('app_index');
        }

        return $this->render('post/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/posts/delete/{id}", name="app_post_delete")
     */
    public function delete(Request $request, $id)
    {
        $entity = $this->postRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException("Posts {$id} is not found.");
        }

        dd($entity);
    }
}
