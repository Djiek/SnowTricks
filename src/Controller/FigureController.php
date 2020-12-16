<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Figure;
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

    function __construct(FigureRepository $repo){
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
            'figures'=>$figures
        ]);
    }

     /**
     * @Route("/create", name="create")
     * @Route("/{id}/edit", name="Figure_edit")
     */
    public function form(Figure $figure = null,Request $request,EntityManagerInterface $manager): Response
    {
        if(!$figure){
            $figure = new Figure();
        }

            $form = $this->createForm(FigureType::class,$figure);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                if (!$figure->getId()) {   
                    $figure->setCreatedAt(new \DateTime());
                } else {
                    $figure->setUpdatedAt(new \DateTime());
                }
             
                $manager->persist($figure);
                $manager->flush();
                return $this->redirectToRoute('trick_show',['id'=> $figure->getId()]);
            }

            return $this->render('figure/createFigure.html.twig', [
                'formFigure' => $form->createView(),
                'editMode' =>$figure->getId() !== null
            ]);

    }

   /**
     * @Route("/show/{id}", name="trick_show")
     */
    public function show(Figure $figure ): Response
    {
        return $this->render('figure/showFigure.html.twig', [
            'figure' => $figure,
             'editMode' =>$figure->getUpdatedAt() !== null
        ]);
    }
}
