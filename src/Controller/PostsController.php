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

        $referer = $request->query->get('referer', 'home');
        $backRoute = $referer === 'list' ? 'app_posts_index' : 'app_home';

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            /** @var \App\Entity\User $user */
            $user = $this->getUser();

            if (!$user->isActive()) {
                $this->addFlash('error', 'Votre compte est en attente de validation par un administrateur.');
                return $this->redirectToRoute('app_post_show', ['id' => $post->getId(), 'referer' => $referer]);
            }

            $comment->setPost($post);
            $comment->setUser($user);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setIsApprouved($this->isGranted('ROLE_ADMIN'));

            $em->persist($comment);
            $em->flush();

            $this->addFlash(
                'success',
                $this->isGranted('ROLE_ADMIN')
                ? 'Commentaire publié.'
                : 'Commentaire soumis, en attente de validation.'
            );

            return $this->redirectToRoute('app_post_show', ['id' => $post->getId(), 'referer' => $referer]);
        }

        $approvedComments = $post->getComments()->filter(
            fn(Comment $c) => $c->isApprouved() === true
        );

        return $this->render('posts/index.html.twig', [
            'post' => $post,
            'comments' => $approvedComments,
            'commentForm' => $form,
            'backRoute' => $backRoute, // ✅
        ]);
    }

    #[Route('/', name: 'app_home')]
    public function home(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['createdAt' => 'DESC'], 3);

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/posts', name: 'app_posts_index')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('posts/list.html.twig', [
            'posts' => $posts,
        ]);
    }
}