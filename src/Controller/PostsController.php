<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
;

class PostsController extends AbstractController
{
    #[Route('/posts/{id}', name: 'app_post_show')]
    public function show(
        int $id,
        PostRepository $postRepository,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $post = $postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Article introuvable');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $comment->setPost($post);
            $comment->setUser($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setIsApprouved(false);

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Commentaire soumis, en attente de validation.');

            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        // Seulement les commentaires approuvés
        $approvedComments = $post->getComments()->filter(
            fn(Comment $c) => $c->isApprouved() === true
        );

        return $this->render('posts/index.html.twig', [
            'post' => $post,
            'comments' => $approvedComments,
            'commentForm' => $form,
        ]);
    }
    #[Route('/posts', name: 'app_posts_index')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();

        return $this->render('posts/list.html.twig', [
            'posts' => $posts,
        ]);
    }
}