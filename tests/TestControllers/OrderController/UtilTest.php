<?php

namespace App\Tests\TestControllers\OrderController;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

use App\Repository\OrderRepository;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UtilTest extends WebTestCase
{


    public function testIndex(): void
    {
        $client = static::createClient();

        // Send a GET request to the /orders route
        $crawler = $client->request('GET', '/orders');

        // Assert that the response is successful (HTTP 200 status code)
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // Assert that the page title contains the word "Orders"
        $this->assertSelectorTextContains('.order_list', 'Orders List');

        // Assert that there is at least one order displayed on the page
        $this->assertGreaterThan(0, $crawler->filter('.order')->count());
    }


    public function testUserOrders()
    {
        $client = static::createClient();
        $client->request('GET', '/user/orders');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login', 302);
    }


    public function testStore(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Simulate an authenticated user
        $user = $entityManager->getRepository(User::class)->findOneByUsername('yassin');
        $client->loginUser($user);

        // Get a product to order
        $product = $entityManager->getRepository(Product::class)->findOneBy([]);

        // Make a request to order the product
        $client->request('GET', '/store/order/' . $product->getId());

        // Check that the response was successful
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

         // Check that the order was added to the database
        $order = $entityManager->getRepository(Order::class)->findOneBy(['user' => $user]);
        $this->assertNotNull($order);
        $this->assertEquals($product->getName(), $order->getPname());
         $this->assertEquals($product->getPrice(), $order->getPrice());  
    }

    public function testUpdateOrderStatus(): void
    {
        // Create a client to make requests to the application
        $client = static::createClient();

        // Get the doctrine EntityManager to interact with the database
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        // Find an existing order to update its status
        $order = $entityManager->getRepository(Order::class)->findOneBy([],['id' => 'ASC']);

        // Make a request to update the order status
        $client->request('GET', '/update/order/' . $order->getId() . '/completed');

        // Assert that the response was successful
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Reload the order from the database to check its new status
        $entityManager->refresh($order);
        $this->assertEquals('completed', $order->getStatus());
    
    }

    public function testDeleteOrder()
    {
        // Create a client to make requests to the application
        $client = static::createClient();

        // Get the doctrine EntityManager to interact with the database
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        // Find an existing order to update its status
        $order = $entityManager->getRepository(Order::class)->findOneBy([],['id' => 'ASC']);
        // get the order ID for the newly created order
        $orderId = $order->getId();

        // send a DELETE request to the deleteOrder endpoint for the new order
        $client->request('DELETE', '/update/order/' . $orderId);

        // assert that the response is a redirect to the orders list
        $this->assertResponseRedirects('/orders');

        // assert that the order was deleted from the database
        $deletedOrder = $entityManager->getRepository(Order::class)->find($orderId);
        $this->assertNull($deletedOrder);
    }



}
