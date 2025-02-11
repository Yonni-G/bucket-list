<?php

namespace App\Controller;

use App\Service\MonService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{

    private $monService;

    // Injection de MyService via le constructeur
    public function __construct(MonService $monService)
    {
        $this->monService = $monService;
    }
    
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        
        $message = $this->monService->saluer('popo');
        
        return $this->render('main/index.html.twig', [
            'message' => $message,
            'myTab' => ['popo', 'pipi']
        ]);
    }

    private function saluer(string $string)
    {
    }
}
