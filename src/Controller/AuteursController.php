<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Auteurs;
use App\Form\AuteursType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AuteursRepository;
use Doctrine\ORM\Mapping\OrderBy;

class AuteursController extends AbstractController
{

    public function __construct(private readonly ManagerRegistry $doctrine){}
        
    #[Route('/auteurs/test', name: 'app_auteurs')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuteursController.php',
        ]);
    }
    
    #[Route('/auteurs/new', name: 'auteurs_new')]
    public function addAuteur(Request $request, EntityManagerInterface $entityManager): Response
    {
        $auteur = new Auteurs();
        $form = $this->createForm(AuteursType::class, $auteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $auteur = $form->getData();
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($auteur);
            $entityManager->flush();
            return $this->redirectToRoute('auteurs');
        }
        return $this->render('auteurs/add.html.twig', [
            'form' => $form->createView(),
        ]);
    } 
    

    //page d'accueil
    #[Route('/auteurs', name: 'auteurs')]
    public function getAuteurs()
    {
        $auteurs = $this->doctrine->getRepository(Auteurs::class)->findAll();
        return $this->render('auteurs/index.html.twig', [
            'auteurs' => $auteurs
        ]);
    }

    #[Route('/auteur/{id}', name: 'auteur')]
    public function getAuteur(Request $request): Response
    {
        $auteur = $this->doctrine->getRepository(Auteurs::class)->find($request->attributes->get('id'));
        
        return $this->render('auteurs/single.html.twig', [
            'auteur' => $auteur,
        ]);
    }
    
    #[Route('/auteur/edit/{id}', name: 'auteur_edit')]
    public function editAuteur($id, Request $request)
    {
        $entityManager = $this->doctrine->getManager();
        $auteur = $entityManager->getRepository(Auteurs::class)->find($id);
        $form = $this->createForm(AuteursType::class, $auteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $auteur = $form->getData();
            $entityManager->flush();
            return $this->redirectToRoute('auteur' , ['id' => $id]);
        }
        return $this->render('auteurs/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/auteur/{id}/articles', name: 'auteur_articles')]
    public function getAuteurArticles(Request $request): Response
    {
        $auteur = $this->doctrine->getRepository(Auteurs::class)->find($request->attributes->get('id'));
        return $this->render('auteurs/articles.html.twig', [
            'auteur' => $auteur
        ]);
    }


    public function deleteAuteur($id)
    {
        $entityManager = $this->doctrine->getManager();
        $auteur = $entityManager->getRepository(Auteurs::class)->find($id);
        $entityManager->remove($auteur);
        $entityManager->flush();
        return $this->redirectToRoute('app_auteurs');
    }
}
