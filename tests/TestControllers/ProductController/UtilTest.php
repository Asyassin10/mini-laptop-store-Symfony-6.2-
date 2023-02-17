<?php
namespace App\Tests\TestControllers\ProductController;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UtilTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        // Send a GET request to the /orders route
        $crawler = $client->request('GET', '/product');

        // Assert that the response is successful (HTTP 200 status code)
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // Assert that the page title contains the word "Orders"
        $this->assertSelectorTextContains('.products_list', 'List of products');

        // Assert that there is at least one order displayed on the page
        $this->assertGreaterThan(0, $crawler->filter('.products')->count());
    }

    public function testStore(): void
    {
        
        $client = static::createClient();

        $crawler = $client->request('GET', '/store/product');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter('form[name="product"]')->form([
            'product[name]' => 'Test Product',
            'product[description]' => 'This is a test product',
            'product[price]' => 19,
            'product[quantity]' => 23,
            'product[image]' => new UploadedFile(
                __DIR__.'/test_image.jpg',
                'test_image.jpg',
                'image/jpeg',
                null,
                true
            )
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/product');
        $client->followRedirect();
    }


    public function testShow(): void
    {
        
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Simulate an authenticated user
        $user = $entityManager->getRepository(User::class)->findOneByUsername('yassin');
        $client->loginUser($user);
        $product = $entityManager->getRepository(Product::class)->findOneBy([]);

        $crawler = $client->request('GET', '/product/details/'.$product->getId());

        // Assert that the response is successful (HTTP 200 status code)
        $this->assertSame(200, $client->getResponse()->getStatusCode());
 
        // Assert that the page title contains the word "Orders"
        $this->assertSelectorTextContains('.product_name', $product->getName());
    }


    public function testEditProduct(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Simulate an authenticated user
        $user = $entityManager->getRepository(User::class)->findOneByUsername('yassin');
        $client->loginUser($user);
        $product = $entityManager->getRepository(Product::class)->findOneBy([]);

        // submit a form to edit the product
        $crawler = $client->request('GET', '/product/edit/'.$product->getId());
        $form = $crawler->selectButton('Submit')->form([
            'product[name]' => 'update Product',
            'product[description]' => 'This is a test product',
            'product[price]' => 19,
            'product[quantity]' => 23,
            'product[image]' => new UploadedFile(
                __DIR__.'/test_image.jpg',
                'test_image.jpg',
                'image/jpeg',
                null,
                true
            )
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/product');
        $client->followRedirect();
    }

    public function testDeleteProduct()
    {
        // Create a client to make requests to the application
        $client = static::createClient();

        // Get the doctrine EntityManager to interact with the database
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        // Find an existing order to update its status
        $product = $entityManager->getRepository(Product::class)->findOneBy([],['id' => 'ASC']);
        // get the product ID for the newly created product
        $productrId = $product->getId();

        // send a DELETE request to the deleteOrder endpoint for the new product
        $client->request('DELETE', '/product/delete/' . $productrId);

        // assert that the response is a redirect to the orders list
        $this->assertResponseRedirects('/product');

        // assert that the product was deleted from the database
        $deletedproductr = $entityManager->getRepository(product::class)->find($productrId);
        $this->assertNull($deletedproductr);
    }


}

