<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\Images;
use App\Form\CommentType;
use App\Form\FigureType;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use App\Service\ImageUpload;
use App\Service\SendImageAndSlug;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureController extends AbstractController
{
    private $repo;

    public function __construct(FigureRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request): Response
    {
        $limit = 9;
        $page = (int)$request->query->get("page", 1);
        $figures = $this->repo->pagination($page, $limit);
        $total = $this->repo->getTotalFigure();

        return $this->render('figure/index.html.twig', [
            'figures' => $figures,
            'total' => $total,
            'limit' => $limit,
            'page' => $page
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @Route("/{slug}/edit", name="Figure_edit")
     */
    public function form(
        Figure $figure = null,
        Request $request,
        EntityManagerInterface $manager,
        SendImageAndSlug $sendImage,
        ImageUpload $imageUpload
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$figure) {
            $figure = new Figure();
        }

        $images = $figure->getImages()->toArray();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sendImage->send($form, $imageUpload, $figure, $manager, $images);
            $figure->setUser($this->getUser());
            if (!$figure->getId()) {
                $message =  $this->addFlash('success', 'La figure a été enregistré en base de donnée avec succés.');
            } else {
                $message = $this->addFlash('success', 'La figure a été mis à jour.');
            }
            $manager->persist($figure);
            $manager->flush();
            $message;
            return $this->redirectToRoute('home');
        }
        return $this->render(
            'figure/createFigure.html.twig',
            ['figure' => $figure, 'formFigure' => $form->createView(), 'editMode' => $figure->getId() !== null]
        );
    }

    /**
     * @Route("/show/{slug}", name="trick_show")
     */
    public function show(
        CommentRepository $repoComment,
        Figure $figure,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $comment = new Comment();
        $limit = 10;
        $page = (int)$request->query->get("page", 1);
        $comments = $repoComment->pagination($page, $limit, $figure->getId());
        $total = $repoComment->getTotalComment($figure->getId());
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                ->setFigure($figure);
            $comment->setUser($this->getUser());
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success', 'Le commentaire a été enregistré en base de donnée avec succés.');
            return $this->redirectToRoute('trick_show', ['slug' => $figure->getSlug()]);
        }
        return $this->render('figure/showFigure.html.twig', [
            'figure' => $figure,
            'commentForm' => $form->createView(),
            'editMode' => $figure->getUpdatedAt() !== null,
            'comment' => $comments,
            'total' => $total,
            'limit' => $limit,
            'page' => $page
        ]);
    }

    /**
     * @Route("/delete/{id}", name="figure_deleted")
     */
    public function delete(Figure $figure, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        foreach ($figure->getImages() as $image) {
            $name = $image->getLink();
            unlink($this->getParameter('images_directory') . '/' . $name);
        }
        $manager->remove($figure);
        $manager->flush();
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/supprime/image/{id}", name="image_deleted", methods={"DELETE"})
     */
    public function deleteImage(Images $image, Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            $name = $image->getLink();
            unlink($this->getParameter('images_directory') . '/' . $name);
            $manager->remove($image);
            $manager->flush();
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
