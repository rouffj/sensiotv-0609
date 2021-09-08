<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/movie/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Iron Sky');
    }

    public function testRegisterForm(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertSelectorTextContains('h2', 'SensioTV+');

        $crawler = $client->clickLink('Register');
        $this->assertEquals('/register', $client->getRequest()->getPathInfo());

        $form = $client->getCrawler()->selectButton('Create your SensioTV account')->form();
        $client->enableProfiler();
        $crawler = $client->submit($form, [
            'register[firstName]' => 'Fabien',
            'register[lastName]' => 'POTENCIER',
            'register[email]' => 'fabien@fabien.io',
            'register[password][first]' => 'test',
            'register[password][second]' => 'test',
            'register[terms]' => true,
        ]);

        //var_dump($client->getResponse()->getContent());die;
        $this->assertResponseIsSuccessful();
        /** @var Symfony\Component\Validator\DataCollector\ValidatorDataCollector $validator */
        //$validator = $client->getProfile()->getCollector('validator');
        //dump($validator);die;
        //$this->assertEquals(1, $validator['violations_count']);
        $this->assertEquals(0, $crawler->filter('.form-error-message')->count());
    }
}
