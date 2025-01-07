<?php

namespace App\Tests\Controller;

use App\Entity\Immobilier;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ImmobilierControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/immobilier/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Immobilier::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Immobilier index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'immobilier[prix]' => 'Testing',
            'immobilier[description]' => 'Testing',
            'immobilier[adresse]' => 'Testing',
            'immobilier[ville]' => 'Testing',
            'immobilier[region]' => 'Testing',
            'immobilier[nbr_chambres]' => 'Testing',
            'immobilier[statut]' => 'Testing',
            'immobilier[image]' => 'Testing',
            'immobilier[utilisateur]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Immobilier();
        $fixture->setPrix('My Title');
        $fixture->setDescription('My Title');
        $fixture->setAdresse('My Title');
        $fixture->setVille('My Title');
        $fixture->setRegion('My Title');
        $fixture->setNbr_chambres('My Title');
        $fixture->setStatut('My Title');
        $fixture->setImage('My Title');
        $fixture->setUtilisateur('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Immobilier');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Immobilier();
        $fixture->setPrix('Value');
        $fixture->setDescription('Value');
        $fixture->setAdresse('Value');
        $fixture->setVille('Value');
        $fixture->setRegion('Value');
        $fixture->setNbr_chambres('Value');
        $fixture->setStatut('Value');
        $fixture->setImage('Value');
        $fixture->setUtilisateur('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'immobilier[prix]' => 'Something New',
            'immobilier[description]' => 'Something New',
            'immobilier[adresse]' => 'Something New',
            'immobilier[ville]' => 'Something New',
            'immobilier[region]' => 'Something New',
            'immobilier[nbr_chambres]' => 'Something New',
            'immobilier[statut]' => 'Something New',
            'immobilier[image]' => 'Something New',
            'immobilier[utilisateur]' => 'Something New',
        ]);

        self::assertResponseRedirects('/immobilier/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getPrix());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getAdresse());
        self::assertSame('Something New', $fixture[0]->getVille());
        self::assertSame('Something New', $fixture[0]->getRegion());
        self::assertSame('Something New', $fixture[0]->getNbr_chambres());
        self::assertSame('Something New', $fixture[0]->getStatut());
        self::assertSame('Something New', $fixture[0]->getImage());
        self::assertSame('Something New', $fixture[0]->getUtilisateur());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Immobilier();
        $fixture->setPrix('Value');
        $fixture->setDescription('Value');
        $fixture->setAdresse('Value');
        $fixture->setVille('Value');
        $fixture->setRegion('Value');
        $fixture->setNbr_chambres('Value');
        $fixture->setStatut('Value');
        $fixture->setImage('Value');
        $fixture->setUtilisateur('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/immobilier/');
        self::assertSame(0, $this->repository->count([]));
    }
}
