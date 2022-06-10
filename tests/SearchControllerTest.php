<?php
namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        chdir(__DIR__);
    }

    public function testRamSearchResponseIsOk()
    {
        $this->client->request('GET', '/xlsx?ram[]=4GB');
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsInt($content->searchCount);
        $this->assertIsArray($content->searchResult);
        $this->assertEquals($content->searchCount, count($content->searchResult));
    }

    public function testMultiRamSearchResponseIsOk()
    {
        $this->client->request('GET', '/xlsx?ram[]=4GB&ram[]=8GB');
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsInt($content->searchCount);
        $this->assertIsArray($content->searchResult);
        $this->assertEquals($content->searchCount, count($content->searchResult));
    }

    public function testRamSearchResponseIsNotOk()
    {
        $this->client->request('GET', '/xlsx?ram[]=9GB');
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }

    public function testLocationSearchResponseIsOk()
    {
        $this->client->request('GET', '/xlsx?location=AmsterdamAMS-01');
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsInt($content->searchCount);
        $this->assertIsArray($content->searchResult);
        $this->assertEquals($content->searchCount, count($content->searchResult));
    }

    public function testLocationSearchResponseIsNotOk()
    {
        $this->client->request('GET', '/xlsx?location=BangaloreBAN-05');
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }

    public function testDiskTypeSearchResponseIsOk()
    {
        $this->client->request('GET', '/xlsx?diskType=SAS');
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsArray($content->searchResult);
        $this->assertIsInt($content->searchCount);
        $this->assertEquals($content->searchCount, count($content->searchResult));
    }

    public function testDiskTypeSearchResponseIsNotOk()
    {
        $this->client->request('GET', '/xlsx?diskType=NVMe');
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }

    public function testStorageFromSearchResponseIsOk()
    {
        $this->client->request('GET', '/xlsx?storageFrom=2TB');
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsArray($content->searchResult);
        $this->assertIsInt($content->searchCount);
        $this->assertEquals($content->searchCount, count($content->searchResult));
    }

    public function testStorageFromSearchResponseIsNotOk()
    {
        $this->client->request('GET', '/xlsx?storageFrom=12GB');
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }

    public function testStorageToSearchResponseIsOk()
    {
        $this->client->request('GET', '/xlsx?storageTo=2TB');
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsArray($content->searchResult);
        $this->assertIsInt($content->searchCount);
        $this->assertEquals($content->searchCount, count($content->searchResult));
    }

    public function testStorageToSearchResponseIsNotOk()
    {
        $this->client->request('GET', '/xlsx?storageTo=12GB');
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }

    public function testStorageFromAndToSearchResponseIsOk()
    {
        $this->client->request('GET', '/xlsx?storageFrom=2TB&storageFrom=4TB');
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsArray($content->searchResult);
        $this->assertIsInt($content->searchCount);
        $this->assertEquals($content->searchCount, count($content->searchResult));
    }

    public function testNoResultFound() {
        $this->client->request('GET', '/xlsx?storageFrom=100TB');
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent());
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals('No data found for the search', $content->error);

    }



}
