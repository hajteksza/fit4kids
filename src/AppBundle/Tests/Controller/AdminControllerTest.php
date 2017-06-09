<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testShowadminpanel()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin');
    }

    public function testShowaddcarouselform()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/addCarousel');
    }

    public function testAddcarousel()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/addCarousel');
    }

}
