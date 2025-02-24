<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;

use App\Helper\Censurator;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/wish', name: 'app_wish_')]
#[IsGranted("ROLE_USER")]
final class WishController extends AbstractController
{

    #[Route('/{id}', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $entityManager, ?Wish $wish, SluggerInterface $slugger, Censurator $censurator): Response
    {

        if(isset($wish) && $wish->getId() == null) {
            throw $this->createNotFoundException('The wish does not exist.');
        }

        $isEdition = isset($wish) && $wish->getId() !== null;

        // Créer une nouvelle instance de l'entité Wish si elle n'existe pas
        $wish = $wish ?? new Wish();
        $wish->setUser($this->getUser());

        // Créer le formulaire basé sur WishType
        $form = $this->createForm(WishType::class, $wish);

        // Traiter la requête (remplir le formulaire avec les données POST)
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            if($form->get('poster_file')->getData() instanceof UploadedFile) {
                $posterFile = $form->get('poster_file')->getData();
                $name = $slugger->slug($wish->getTitle()). '-' . uniqid() . '.' . $posterFile->guessExtension();
                $posterFile->move('images/uploads', $name);

                if($wish->getPoster() && file_exists('images/uploads/' . $wish->getPoster())) {
                    unlink('images/uploads/' . $wish->getPoster());
                }

                $wish->setPoster($name);
            }

            // On applique la censure à l'aide de notre helper
            $wish->setTitle($censurator->purify($wish->getTitle()));
            $wish->setDescription($censurator->purify($wish->getDescription()));


            /* AVANT LA PRE PERSIST SUR L ENTITY

            // Si c'est une création, on met la date de création
            if(!$isEdition) {
                $wish->setDateCreated(new \DateTime());
            }
            */
            // Sauvegarder l'entité dans la base de données
            $entityManager->persist($wish);
            $entityManager->flush();

            // Rediriger ou afficher un message de succès
            $flash = $isEdition ? ['type' => 'warning', 'message' => 'Wish was updated!'] : ['type' => 'success', 'message' => 'Wish was created!'];
            $this->addFlash($flash['type'], $flash['message']);
            return $this->redirectToRoute('app_wish_list');
        }

        // Afficher le formulaire dans la vue
        return $this->render('wish/new.html.twig', [
            'form' => $form
        ]);
    }

    /* AVANT PAGINATOR

    #[Route('/list/{page}', name: 'list', requirements: ['page' => '\d+'], defaults: ['page' => 1], methods: ['GET'])]
    public function index(int $page, WishRepository $wishRepository): Response
    {
        $limit = 2; // Nombre d'éléments par page
        $offset = ($page - 1) * $limit;

        // Récupérer les éléments paginés
        $wishes = $wishRepository->findBy([], ['dateCreated' => 'DESC'], $limit, $offset);

        // Nombre total d'éléments
        $totalWishes = $wishRepository->count([]);

        // Nombre total de pages (arrondi à l'entier supérieur)
        $totalPages = ceil($totalWishes / $limit);

        // Vérifier si une page suivante existe
        $hasNextPage = $page < $totalPages;

        // Vérifier si une page précédente existe
        $hasPreviousPage = $page > 1;

        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes,
            'page' => $page,
            'totalPages' => $totalPages,
            'hasNextPage' => $hasNextPage,
            'hasPreviousPage' => $hasPreviousPage,
        ]);
    }

    */

    #[Route('/list/{page}', name: 'list', requirements: ['page' => '\d+'], defaults: ['page' => 1], methods: ['GET'])]
    #[isGranted('ROLE_USER')]
    public function index(int $page, Request $request, WishRepository $wishRepository, PaginatorInterface $paginator): Response
    {


        //dd($this->getUser());

        $query = $wishRepository->createQueryBuilder('w')
            ->orderBy('w.dateCreated', 'DESC')
            ->getQuery();

        $wishes = $paginator->paginate(
            $query, // Requête Doctrine
            $request->query->getInt('page', 1),
            2 // Nombre d'éléments par page
        );


        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes,

        ]);
    }



    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, EntityManagerInterface $entityManager, Wish $wish): Response
    {
        if ($this->isCsrfTokenValid('delete'.$wish->getId(), $request->request->get('_token'))) {
            $entityManager->remove($wish);
            $entityManager->flush();

        }
        return $this->redirectToRoute('app_wish_list');
    }

    #[Route('/{id}/details', name: 'detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(EntityManagerInterface $entityManager, Wish $wish): Response
    {
        return $this->render('wish/detail.html.twig', [
            'wish' => $wish,

        ]);
    }
}
