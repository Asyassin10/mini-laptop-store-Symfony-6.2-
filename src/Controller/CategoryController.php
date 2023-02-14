<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }
     
    #[Route('/store/category', name: 'app_category')]
    public function store(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class,$category);

        return $this->renderForm('category/create.html.twig', [
            'form' => $form
        ]);
    }
}
