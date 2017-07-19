<?php

namespace Tests\AppBundle\DataAccess;

use AppBundle\DataAccess\AdminData;
use AppBundle\DataAccess\GroupeData;
use AppBundle\DataAccess\UserData;
use AppBundle\Entity\LogInForm;
use AppBundle\Entity\SignInForm;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class GroupeDataTest extends KernelTestCase{
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

    public function testaddGroupe(){
        $sign = new LogInForm();
        $sign->setEmail('EPITA');
        AdminData::addGroupe($this->em,$sign);
        $this->assertCount(1,GroupeData::getAll($this->em));
    }

    public function testContent(){
        $this->assertCount(0,GroupeData::getAll($this->em));
    }



    public function testaddToUser(){
        $this->testaddGroupe();
        $sign = new SignInForm();
        $sign->setFirstname('user');
        $sign->setEmail('mail');
        $sign->setTelephone('1234');
        $sign->setLastname('test');
        $sign->setPassword('1234');
        $sign->setConfpassword('1234');
        $this->assertEquals('OK',UserData::insertUser($sign,$this->em));
        GroupeData::addToUser($this->em,1,1);
        $this->assertCount(1,GroupeData::getGroupeByUser($this->em,1));
    }


    public function testaddToUserFail(){
        $this->testaddToUser();
        GroupeData::addToUser($this->em,1,1);
        $this->assertCount(1,GroupeData::getGroupeByUser($this->em,1));
    }

    public function testremoveFromUser(){
        $this->testaddToUser();
        GroupeData::removeFromUser($this->em,1,1);
        $this->assertCount(0,GroupeData::getGroupeByUser($this->em,1));
    }

    public function testgetGroupeNotOwnedByUse(){
        $this->testaddToUser();
        $this->assertCount(0,GroupeData::getGroupeNotOwnedByUser($this->em,1));
        $sign = new LogInForm();
        $sign->setEmail('BIOTECH');
        AdminData::addGroupe($this->em,$sign);
        $this->assertCount(1,GroupeData::getGroupeNotOwnedByUser($this->em,1));

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
