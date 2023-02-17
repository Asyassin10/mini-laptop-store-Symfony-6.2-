<?php

namespace App\Tests\TestControllers\HomeController;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UtilTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Home');
     }

    public function testCategoryProducts()
    {
        $client = static::createClient();

        // Assuming there is at least one category in the database
        $category = $client->getContainer()->get('doctrine')->getRepository(Category::class)->findOneBy([]);

        $crawler = $client->request('GET', '/product/'.$category->getId());
        $this->assertSelectorTextContains('.category_name', $category->getName());
        $this->assertResponseIsSuccessful();
     }
}
