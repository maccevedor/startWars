<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CharacterRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Character;

class CharacterController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }
    
    /**
     * @Route("/character", name="character")
     */
    public function index(CharacterRepository $characterRepository): Response
    {
        $characters = $characterRepository->findAll();

        return $this->render('character/index.html.twig', [
            'characters' => $characters,
        ]);
    }

    /**
     * @Route("/character/show/{id}", name="character_show")
     */
    public function show(Character $character): Response
    {

        return $this->render('character/show.html.twig', [
            'character' => $character,
        ]);
    }
   
}
