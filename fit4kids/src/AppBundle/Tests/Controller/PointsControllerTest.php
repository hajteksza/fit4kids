<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PointsControllerTest extends WebTestCase
{
    public function testAddpoints()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/addPoints');
    }

}
