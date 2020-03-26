<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Event\PostPreCreatedEvent;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PostController extends AbstractController
{
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/posts/new", name="app_post_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EventDispatcherInterface $dispatcher, UserInterface $user)
    {
        $entity = new Post();
        $entity->setUser($user);
        $entity->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm('App\Form\PostType', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new PostPreCreatedEvent($entity);
            $dispatcher->dispatch($event);

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
     * @Route("/posts/edit/{slug}", name="app_post_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, $slug)
    {
        $entity = $this->postRepository->findOneBySlug($slug);
        if (!$entity) {
            throw $this->createNotFoundException("Posts {$slug} is not found.");
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
     * @Route("/posts/delete/{slug}", name="app_post_delete", methods={"GET"})
     */
    public function delete(Request $request, $slug)
    {
        if (!$this->isCsrfTokenValid('delete', $request->query->get('token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('app_index');
        }

        $entity = $this->postRepository->findOneBySlug($slug);
        if (!$entity) {
            throw $this->createNotFoundException("Posts {$slug} is not found.");
        }

        $this->addFlash('success', '文章已删除成功！');

        return $this->redirectToRoute('app_index');
    }

    /**
     * @Route("/posts/{slug}", name="app_post_show", methods={"GET", "POST"})
     */
    public function show(Request $request, $slug)
    {
        $entity = $this->postRepository->findOneBySlug($slug);
        if (!$entity) {
            throw $this->createNotFoundException("Posts {$slug} is not found.");
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

            return $this->redirectToRoute('app_post_show', ['slug' => $slug, '_fragment' => $fragment]);
        }

        return $this->render('post/show.html.twig', [
            'post' => $entity,
            'comment_form' => $form->createView(),
        ]);
    }
}
