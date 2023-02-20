<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;



class HomeController extends AbstractController
{
    private $productRepository;
    private $categoryRepository;
    private $entityManager;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        ManagerRegistry $doctrine)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $doctrine->getManager();
    }



    #[Route('/{_locale}/home', name: 'home')]
    public function index(): Response
    {   
        $products = $this->productRepository->findAll();
        $categories = $this->categoryRepository->findAll();
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    #[Route('/{_locale}/product/{category}', name: 'product_category')]
    public function categoryProducts(Category $category): Response
    {
        $categories = $this->categoryRepository->findAll();
        return $this->render('home/index.html.twig', [
            'products' => $category->getProducts(),
            'categories' => $categories,
        ]);
    }

}
