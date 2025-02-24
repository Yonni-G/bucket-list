<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{

    // src/Controller/UserController.php
    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/avatar.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/user', name: 'app_user', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'User created!');
            return $this->redirectToRoute('app_user');
        }
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form
        ]);
    }
}
