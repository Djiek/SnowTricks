<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Figure;
use App\Form\CommentType;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureController extends AbstractController
{

    private $repo;

    function __construct(FigureRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $figures = $this->repo->findAll();
        return $this->render('figure/index.html.twig', [
            'controller_name' => 'FigureController',
            'figures' => $figures
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @Route("/{id}/edit", name="Figure_edit")
     */
    public function form(Figure $figure = null, Request $request, EntityManagerInterface $manager): Response
    {
        if (!$figure) {
            $figure = new Figure();
        }

        $form = $this->createForm(FigureType::class, $figure);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$figure->getId()) {
                $figure->setCreatedAt(new \DateTime());
            } else {
                $figure->setUpdatedAt(new \DateTime());
            }
            $figure->setUser($this->getUser());
            $manager->persist($figure);
            $manager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('figure/createFigure.html.twig', [
            'formFigure' => $form->createView(),
            'editMode' => $figure->getId() !== null
        ]);
    }

    /**
     * @Route("/show/{id}", name="trick_show")
     */
    public function show(Figure $figure, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                ->setFigure($figure);
            $comment->setUser($this->getUser());
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('trick_show', ['id' => $figure->getId()]);
        }

        return $this->render('figure/showFigure.html.twig', [
            'figure' => $figure,
            'commentForm' => $form->createView(),
            'editMode' => $figure->getUpdatedAt() !== null
        ]);
    }

    /**
     * @Route("/delete/{id}", name="figure_deleted", methods= "DELETE")
     */
    public function delete(Figure $figure, EntityManagerInterface $manager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $figure->getId(), $request->get('_token'))) {
             $manager->remove($figure);
             $manager->flush();
        }
         return $this->redirectToRoute('home');
    }
}
