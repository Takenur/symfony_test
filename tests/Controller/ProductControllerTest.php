<?php

namespace App\Tests\Controller;

class ProductControllerTest extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    public function testParseProductSuccessfully()
    {

        $client = static::createClient();

        $validUrl = 'https://world.openfoodfacts.org/product/7622210449283/prince-chocolate-cookies-lu';


        $client->request('POST', '/product/parse', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'url' => $validUrl
        ]));


        $this->assertResponseIsSuccessful();


        $this->assertEquals(200, $client->getResponse()->getStatusCode());


        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('success', $responseData);
        $this->assertTrue($responseData['success']);

        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('title', $responseData['data']);
        $this->assertArrayHasKey('description', $responseData['data']);
        $this->assertArrayHasKey('photo_path', $responseData['data']);


        $this->assertEquals('Prince Chocolate Cookies - Lu - 100g', $responseData['data']['title']); // Пример проверки имени
    }

    public function testParseProductWithInvalidUrl()
    {

        $client = static::createClient();


        $invalidUrl = 'https://example.com/invalid-url';


        $client->request('POST', '/product/parse', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'url' => $invalidUrl
        ]));


        $this->assertEquals(422, $client->getResponse()->getStatusCode());


        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('url is not valid', $responseData['message']);
    }

    public function testParseProductWithEmptyUrl()
    {

        $client = static::createClient();


        $client->request('POST', '/product/parse', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'url' => ''
        ]));

        $this->assertEquals(422, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('url is empty', $responseData['message']);
    }
}