<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends AbstractController
{
    #[Route('/articles', name: 'app_articles')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ArticlesController.php',
        ]);
    }
    
    public function addArticle()
    {
        return $this->render('articles/addArticle.html.twig');
    }
    
    #[Route('/article/{id}', name: 'article')]
    public function getArticle($id)
    {
        return $this->render('articles/showArticle.html.twig', [
            'id' => $id,
        ]);
    }
    
    #[Route('/article/edit/{id}', name: 'app_articles')]
    public function editArticle($id)
    {
        return $this->render('articles/editArticle.html.twig', [
            'id' => $id,
        ]);
    }
    
    public function deleteArticle($id)
    {
        return $this->render('articles/deleteArticle.html.twig', [
            'id' => $id,
        ]);
    }
    
}
