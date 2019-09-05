<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Request\CreatePostRequest;
use App\Service\PostExporter;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use League\Csv\CannotInsertRecord;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route(path="posts/")
 */
class PostController
{
    /**
     * @Route(path="", name="guestbook", methods={"GET"})
     * @param TokenStorageInterface $storage
     * @param Environment $twig
     * @param PostRepository $repository
     * @param PaginatorInterface $pager
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(
        TokenStorageInterface $storage,
        Environment $twig,
        PostRepository $repository,
        PaginatorInterface $pager,
        Request $request
    ): Response
    {
        $user = $storage->getToken()->getUser();
        $posts = $repository->findLatestPosts();
        $pagination = $pager->paginate(
            $posts,
            $request->query->getInt('page', 1),
            10
        );
        $content = $twig->render('post/index.html.twig', [
            'posts' => $posts,
            'currentUser' => $user,
            'pagination' => $pagination
        ]);

        return new Response($content);
    }

    /**
     * @Route(path="show/{post}", name="guestbook_show", methods={"GET"})
     * @param Post $post
     * @param Environment $twig
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(Post $post, Environment $twig): Response
    {
        $content = $twig->render('post/show.html.twig', ['post' => $post]);

        return new Response($content);
    }

    /**
     * @Route(path="create", name="guestbook_create_view", methods={"GET"})
     * @param Environment $twig
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderView(Environment $twig): Response
    {
        $content = $twig->render('post/create.html.twig');

        return new Response($content);
    }

    /**
     * @Route(path="create", name="guestbook_create", methods={"POST"})
     * @param CreatePostRequest $request
     * @param EntityManagerInterface $em
     * @param RouterInterface $router
     * @return Response
     */
    public function create(CreatePostRequest $request, EntityManagerInterface $em, RouterInterface $router): Response
    {
        $post = new Post(
            $request->body,
            $request->author
        );

        $em->persist($post);
        $em->flush();

        return new RedirectResponse(
            $router->generate('guestbook_show', [
                'post' => $post->getId()
            ])
        );
    }


    /**
     * @Route(path="delete/{post}", name="guestbook_delete", methods={"POST"})
     * @param Post $post
     * @param EntityManagerInterface $em
     * @param RouterInterface $router
     * @param FlashBagInterface $flashBag
     * @return Response
     */
    public function delete(
        Post $post,
        EntityManagerInterface $em,
        RouterInterface $router,
        FlashBagInterface $flashBag
    ): Response
    {
        $em->remove($post);
        $em->flush();

        $flashBag->add('delete', "Post was successfully deleted");
        return new RedirectResponse($router->generate('guestbook'));
    }

    /**
     * @Route(path="export", name="guestbook_export",methods={"GET"})
     * @param PostExporter $exporter
     * @param PostRepository $postRepository
     * @return Response
     * @throws CannotInsertRecord
     */
    public function export(PostExporter $exporter, PostRepository $postRepository): Response
    {
        $response = new Response(
            $exporter->export($postRepository->findLatestPosts())
        );

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            date("Y-m-d_H-i-s") . ".csv"
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-type', 'text/csv');

        return $response;
    }
}
