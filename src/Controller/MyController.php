<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\RequestStack;

use Doctrine\DBAL\Schema\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


class MyController extends AbstractController
{
    private $requestStack;
    private $translator;

    public function __construct(RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    
    #[Route('/', name: 'index')]
    public function index()
    {   
        return $this->redirectToRoute('home');

    }
}
