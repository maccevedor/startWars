<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FilmRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Film;
use App\Entity\Character;
use App\Entity\Specie;

class FilmController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/film", name="film")
     */
    public function index(FilmRepository $filmRepository): Response
    {
        $specieRepository = $this->getDoctrine()->getRepository(Specie::class);
        $characterRepository = $this->getDoctrine()->getRepository(Character::class);
        $films = $filmRepository->findAll();

        if ($films) {
        } else {
            $url = 'http://swapi.dev/api/films';
            $contents = $this->getJson($url);
            foreach ($contents['results'] as $content) {
                $film = $filmRepository->findOneBy(['url' => $content['url']]);
                if ($film) {
                } else {
                    $film = new Film();
                }
                $film->setDirector($content['director']);
                $film->setTitle($content['title']);
                $film->setReleaseDate(new \DateTimeImmutable($content['release_date']));
                $film->setUrl($content['url']);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($film);
                $entityManager->flush();
                foreach ($content['characters'] as $character) {
                    $urlCharacter = $character;
                    $characters = $this->getJson($urlCharacter);
                    unset($specie);
                    if ($characters['species']) {
                        foreach ($characters['species'] as $specie) {
                            $species = $this->getJson($specie);
                            $specie = $specieRepository->findOneBy(['url' => $species['url']]);
                            if ($specie) {
                            } else {
                                $specie = new Specie();
                            }
                            $specie->setName($species['name']);
                            $specie->setClassification($species['classification']);
                            $specie->setDesignation($species['designation']);
                            $specie->setUrl($species['url']);
                            $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->persist($specie);
                            $entityManager->flush();
                        }
                    }
                    $character = $characterRepository->findOneBy(['url' => $characters['url']]);
                    if ($character) {
                    } else {
                        $character = new Character();
                    }
                    if (isset($specie)) {
                        $character->setSpecie($specie);
                    }
                    $character->setName($characters['name']);
                    $character->setGender($characters['gender']);
                    $character->setUrl($characters['url']);
                    $character->addFilm($film);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($character);
                    $entityManager->flush();
                }
            }
            $films = $filmRepository->findAll();
        }
        return $this->render('film/index.html.twig', [
            'films' => $films,
        ]);
    }

    /**
     * @Route("/film/show/{id}", name="film_show", methods={"GET"})
     */
    public function show(Film $film): Response
    {

        return $this->render('film/show.html.twig', [
            'film' => $film,
        ]);
    }
    /**
     * Get information from Api
     */
    public function getJson($url)
    {
        $response = $this->client->request(
            'GET',
            $url
        );
        $statusCode = $response->getStatusCode();
        if ($statusCode == '200') {
            $contentType = $response->getHeaders()['content-type'][0];
            $contents = $response->getContent();
            $contents = $response->toArray();
        } else {
        }
        return $contents;
    }
}
