<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['POST'])]
    public function index(Request $request): Response
    {
        //dump($request);



        // Récupération de la requête
        $searchQuery = $request->request->get('search');

        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }
}
