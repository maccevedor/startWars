<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Specie;
use App\Repository\SpecieRepository;

class SpecieController extends AbstractController
{
    /**
     * @Route("/specie", name="specie")
     */
    public function index(SpecieRepository $specieRepository): Response
    {
        return $this->render('specie/index.html.twig', [
            'species' => $specieRepository->findAll(),
        ]);
    }
}
