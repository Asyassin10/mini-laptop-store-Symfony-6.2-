<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class OrderController extends AbstractController
{
    private $orderRepository;
    private $entityManager;

    public function __construct(
        OrderRepository $orderRepository,
        ManagerRegistry $doctrine)
    {
        $this->orderRepository = $orderRepository;
        $this->entityManager = $doctrine->getManager();
    }
       
    #[Route('/orders', name: 'orders_list')]

    public function index(): Response
    {
        $orders = $this->orderRepository->findAll();
        return $this->render('order/index.html.twig', [
            'orders' => $orders
        ]);
    }
    
    #[Route('/user/orders', name: 'user_order_list')]
    public function userOrders(): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('order/user.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/store/order/{product}', name: 'order_store')]
    public function store(Product $product): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        $orderExists = $this->orderRepository->findOneBy([
            'user' => $this->getUser(),
            'pname' => $product->getName()
        ]);

        if($orderExists){
            $this->addFlash(
                'warning',
                'Your have already ordered this product'
            );
            return $this->redirectToRoute('user_order_list');
        }

        $order = new Order();
        $order->setPname($product->getName());
        $order->setPrice($product->getPrice());
        $order->setStatus('processing...');
        $order->setUser($this->getUser());
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $this->entityManager->persist($order);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();
        $this->addFlash(
            'success',
            'Your order was saved'
        );
        return $this->redirectToRoute('user_order_list');
    }

    #[Route('/update/order/{order}/{status}', name: 'order_status_update')]

    public function updateOrderStatus(Order $order,$status): Response
    {
        $order->setStatus($status);
        $this->entityManager->persist($order);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();
        $this->addFlash(
            'success',
            'Your order status was updated'
        );
        return $this->redirectToRoute('orders_list');
    }

    #[Route('/update/order/{order}', name: 'order_delete')]

    public function deleteOrder(Order $order): Response
    {
        $this->entityManager->remove($order);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();
        $this->addFlash(
            'success',
            'Your order was deleted'
        );
        return $this->redirectToRoute('orders_list');
    }

    
}
