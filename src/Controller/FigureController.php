<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Figure;
use App\Entity\Images;
use App\Entity\Videos;
use App\Form\CommentType;
use App\Form\FigureType;
use App\Form\VideoType;
use App\Repository\FigureRepository;
use App\Service\ImageUpload;
use App\Service\Slug;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Expression;

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
        $figures = $this->repo->findBy(array(), array('createdAt' => 'desc'));
        return $this->render('figure/index.html.twig', [
            'controller_name' => 'FigureController',
            'figures' => $figures
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @Route("/{slug}/edit", name="Figure_edit")
     */
    public function form(Figure $figure = null, Request $request, EntityManagerInterface $manager, ImageUpload $imageUpload): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$figure) {
            $figure = new Figure();
            $slug = new Slug();
        }

        $form = $this->createForm(FigureType::class, $figure);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $fileName = $imageUpload->upload($image->getFile());
                $image->setLink($fileName);
                $figure->addImage($image);
            }
            if (!$figure->getId()) {
                $figure->setCreatedAt(new \DateTime());
                $slugName = $slug->createSlug($figure->getName());
                $figure->setSlug($slugName);
            } else {
                $figure->setUpdatedAt(new \DateTime());
                $lastSlug = $figure->getSlug();
                $slug = new Slug();
                $nameWithoutSlug = $slug->deleteSlug($lastSlug);
                if ($figure->getName() != $nameWithoutSlug) {
                    $slugName = $slug->createSlug($figure->getName());
                    $figure->setSlug($slugName);
                }
            }

            $figure->setUser($this->getUser());


            $manager->persist($figure);
            $manager->flush();

            $this->addFlash('success', 'La figure a été enregistré en base de donnée avec succés.');
            return $this->redirectToRoute('home');
        }

        return $this->render('figure/createFigure.html.twig', [
            'figure' => $figure,
            'formFigure' => $form->createView(),
            'editMode' => $figure->getId() !== null
        ]);
    }

    /**
     * @Route("/show/{slug}", name="trick_show")
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

            return $this->redirectToRoute('trick_show', ['slug' => $figure->getSlug()]);
        }

        return $this->render('figure/showFigure.html.twig', [
            'figure' => $figure,
            'commentForm' => $form->createView(),
            'editMode' => $figure->getUpdatedAt() !== null
        ]);
    }

    /**
     * @Route("/delete/{id}", name="figure_deleted")
     */
    public function delete(Figure $figure, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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
