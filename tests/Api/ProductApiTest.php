<?php

namespace App\Tests\Api;

use App\Entity\Brand;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductApiTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;

    private function getHeaders($withAuth = true): array
    {
        if (!$withAuth)
            return [
                'CONTENT_TYPE' => 'application/json',
            ];

        return [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Basic ' . base64_encode('admin@mail.com:admin')
        ];
    }

    private function makeRequest($client, $method, $url, $headers = [], $body = null)
    {
        $client->request(
            $method,
            $url,
            [],
            [],
            $headers,
            $body ? json_encode($body) : null
        );
        return $client;
    }

    private function getJsonResponse($client)
    {
        $responseData = $client->getResponse()->getContent();
        $this->assertJson($responseData);
        return json_decode($responseData, true);
    }


    private function initiliazeBody($name, $price, $year=2022, $energy='diesel'): array
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $category = $this->entityManager->getRepository(Category::class)->findOneBy([]);
        $brand = $this->entityManager->getRepository(Brand::class)->findOneBy([]);

        $this->assertNotNull($category, 'No category found in the database.');
        $this->assertNotNull($brand, 'No brand found in the database.');

        $body =  [
            'description' => 'Description',
            'stock' => 10,
            'brand' => '/api/brands/'.$brand->getId(),
            'price' => $price,
            'category' => 'api/categories/'.$category->getId(),
            'year' => $year,
            'energy' => $energy
        ];

        if ($name) {
            $body['name'] = $name;
        }

        return $body;
    }

    public function testUnauthorizedAccess()
    {
        $client = static::createClient();

        $this->makeRequest(
            $client,
            'POST', 
            '/api/products', 
            $this->getHeaders(false), 
            $this->initiliazeBody('BMW X5', 9000)
        );

        $this->assertResponseStatusCodeSame(401);
        $responseData = $client->getResponse()->getContent();
        $this->assertJson($responseData);

        $this->assertStringContainsString('Unauthorized', $responseData);
    }

    public function testCreateProductWithMissingFields()
    {
        $client = static::createClient();

        $this->makeRequest(
            $client,
            'POST',
            '/api/products',
            $this->getHeaders(),
            $this->initiliazeBody(null, 9000)
        );
       
        $this->assertResponseStatusCodeSame(422); // Bad request
       
        $data = $this->getJsonResponse($client);

        $this->assertArrayHasKey('violations', $data);
        $this->assertCount(1, $data['violations']);
        $this->assertEquals('This value should not be blank.', $data['violations'][0]['message']);
        $this->assertEquals('name', $data['violations'][0]['propertyPath']);
    }


    public function testCreateProduct()
    {
        $client = static::createClient();
        $productName = 'BMW X5';

        $this->makeRequest(
            $client,
            'POST',
            '/api/products',
            $this->getHeaders(),
            $this->initiliazeBody($productName, 9000, 2000)
        );

        $this->assertResponseStatusCodeSame(201);
        $responseData = $client->getResponse()->getContent();
        $this->assertJson($responseData);
        $this->assertStringContainsString('"name":"'. $productName.'"', $responseData);
    }

    public function testDuplicateProductName()
    {
        $client = static::createClient();

        $productName = 'BWN X6';

        $this->makeRequest(
            $client,
            'POST',
            '/api/products',
            $this->getHeaders(),
            $this->initiliazeBody($productName, 9000)
        );

        $this->makeRequest(
            $client,
            'POST',
            '/api/products',
            $this->getHeaders(),
            $this->initiliazeBody($productName, 9000)
        );

      
        $data = $this->getJsonResponse($client);
        $this->assertArrayHasKey('hydra:title', $data);
        $this->assertEquals('An error occurred', $data['hydra:title']);
        $this->assertArrayHasKey('hydra:description', $data);
        $this->assertStringContainsString('The entity violates a unique constraint', $data['hydra:description']);

    }
    
    public function testUpdateProductNotFound()
    {
        $client = static::createClient();
      
        $client = $this->makeRequest(
            $client,
            'PUT',
            '/api/products/999',
            $this->getHeaders(),
            [
                'stock' => 40
            ]
        );
       
        $data = $this->getJsonResponse($client);
        $this->assertResponseStatusCodeSame(404);

        $this->assertArrayHasKey('hydra:title', $data);
        $this->assertEquals('An error occurred', $data['hydra:title']);
        $this->assertArrayHasKey('hydra:description', $data);
        $this->assertStringContainsString('Not Found', $data['hydra:description']);
    }
}
