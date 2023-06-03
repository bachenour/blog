<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Articles;
use App\Form\ArticlesType;

class ArticlesController extends AbstractController
{

    public function __construct(private readonly ManagerRegistry $doctrine){}
    
    #[Route('/articles/test', name: 'app_articles')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ArticlesController.php',
        ]);
    }
    
    #[Route('/articles/new', name: 'articles_add')]
    public function addArticle(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Articles();
        $form = $this->createForm(
            ArticlesType::class, 
            $article,
            [
                'action' => $this->generateUrl('articles_add'),
                'method' => 'POST',
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $article->getAuteur() != null) {
            $article = $form->getData();
            $date = new \DateTime();
            $article->setDateCreation($date);
            $article->setDateModification($date);
            $article->setDatePublication($date);
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('articles_add');
        }
        return $this->render('articles/add.html.twig', [
            'form' => $form->createView(),
        ]);
    } 
    
    #[Route('/articles', name: 'articles')]
    public function getArticles(): Response
    {
        $articles = $this->doctrine
            ->getRepository(Articles::class)
            ->findBy([], ['date_publication' => 'DESC']);
        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/{id}', name: 'article')]
    public function getArticle(Request $request): Response
    {
        $article = $this->doctrine->getRepository(Articles::class)->find($request->attributes->get('id'));
        
        return $this->render('articles/single.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/edit/{id}', name: 'article_edit')]
    public function editArticle($id, Request $request)
    {
        $entityManager = $this->doctrine->getManager();
        $article = $entityManager->getRepository(Articles::class)->find($id);
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $entityManager->flush();
            return $this->redirectToRoute('article' , ['id' => $id]);
        }
        return $this->render('articles/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
}
