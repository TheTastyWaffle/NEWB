<?php

namespace Tests\AppBundle\DataAccess;

use AppBundle\DataAccess\AdminData;
use AppBundle\Entity\LogInForm;
use AppBundle\Entity\SignInForm;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class AdminDataTest extends KernelTestCase{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $session;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();
        static::$kernel = static::createKernel();
        static::$kernel -> boot();
        $this -> em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->regenerateSchema();
        $this->session = new Session(new MockArraySessionStorage());
    }

    /**
     * Drops current schema and creates a brand new one
     */
    protected function regenerateSchema() {
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadatas)) {
            $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
            $tool -> dropSchema($metadatas);
            $tool -> createSchema($metadatas);
        }
    }

    public function test_is_not_admin() {
        $this->assertEquals(false, AdminData::isAdmin($this->em, $this->session));
    }

    public function test_is_Admin() {
        AdminData::addAdmin($this->em, $this->session->getId());
        $this->assertNotEquals(false, $this->em, AdminData::isAdmin($this->em,$this->session));
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}
