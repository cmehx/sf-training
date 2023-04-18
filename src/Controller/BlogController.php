<?php 

// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController
{
    #[Route('/name/{name}', name: 'display_name')]
    public function show(Request $request, string $name): Response
    {
         return $this->render('test/index.html.twig', [
            'name' => $name,
            'age'  => $request->query->get('age')
        ]);
    }
}