<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Artist;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArtistController extends AbstractController
{
    #[Route('/artist', name: 'app_artist', methods:['GET'])]
    public function index(): Response
    {
        return $this->render('artist/index.html.twig', [
            'controller_name' => 'ArtistController',
        ]);
    }

    #[Route('/artist', name: 'app_artist', methods:['POST'])]
    public function create(): Response
    {
        return $this->render('artist/index.html.twig', [
            'controller_name' => 'ArtistController',
        ]);
    }

    #[Route('/artist/create', name: 'create_artist')]
    public function createArtist(EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $artist = new Artist();
        $artist->setName("     Bollos lazee     monkey              ");
        $artist->setFirstName("     azeazeazeazeazez       ");
        
        $errors = $validator->validate($artist);

        if (count($errors)) {
            return new Response((string) $errors, 400);
        }
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($artist);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id '.$artist->getId());
    }
}
