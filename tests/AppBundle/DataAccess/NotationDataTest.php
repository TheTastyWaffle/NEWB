<?php

namespace Tests\AppBundle\DataAccess;

use AppBundle\DataAccess\AdminData;
use AppBundle\DataAccess\NotationData;
use AppBundle\DataAccess\ProjectData;
use AppBundle\DataAccess\TechData;
use AppBundle\DataAccess\UserData;
use AppBundle\Entity\LogInForm;
use AppBundle\Entity\ProjectForm;
use AppBundle\Entity\SignInForm;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class NotationDataTest extends KernelTestCase{
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

    public function testAddNotation() {
        $sign = new LogInForm();
        $sign->setPassword('HASKELL');
        AdminData::addTech($this->em,$sign);
        $this->assertCount(1,TechData::getAll($this->em));
        $sign = new SignInForm();
        $sign->setFirstname('user');
        $sign->setEmail('mail');
        $sign->setTelephone('1234');
        $sign->setLastname('test');
        $sign->setPassword('1234');
        $sign->setConfpassword('1234');
        $this->assertEquals('OK',UserData::insertUser($sign,$this->em));
        TechData::addToUser($this->em,1,1);
        $this->assertCount(1,TechData::getTechByUser($this->em,1,1));
        NotationData::addNotation($this->em,1,1,1,3);
        $this->assertEquals(3,NotationData::getNotationForTech($this->em,1,1));

    }


    public function testAddNotationHuman(){
        $sign = new SignInForm();
        $sign->setFirstname('user');
        $sign->setEmail('mail');
        $sign->setTelephone('1234');
        $sign->setLastname('test');
        $sign->setPassword('1234');
        $sign->setConfpassword('1234');
        $this->assertEquals('OK',UserData::insertUser($sign,$this->em));
        NotationData::addNotationHuman($this->em,1,1,3);
        $this->assertEquals(3,NotationData::getNotationForHuman($this->em,1));
    }

    public function testMoyenneTech(){
        $this->testAddNotation();
        $sign = new LogInForm();
        $sign->setPassword('LISP');
        AdminData::addTech($this->em,$sign);
        $this->assertCount(2,TechData::getAll($this->em));
        TechData::addToUser($this->em,1,2);
        $this->assertCount(2,TechData::getTechByUser($this->em,1,1));
        NotationData::addNotation($this->em,1,2,1,5);
        $this->assertEquals(4,NotationData::getAVGTech($this->em,1));
    }

    public function testCanNote(){
        $sign = new LogInForm();
        $sign->setPassword('HASKELL');
        AdminData::addTech($this->em,$sign);
        $this->assertCount(1,TechData::getAll($this->em));
        $sign = new SignInForm();
        $sign->setFirstname('user');
        $sign->setEmail('mail');
        $sign->setTelephone('1234');
        $sign->setLastname('test');
        $sign->setPassword('1234');
        $sign->setConfpassword('1234');
        $this->assertEquals('OK',UserData::insertUser($sign,$this->em));
        TechData::addToUser($this->em,1,1);
        $this->assertCount(1,TechData::getTechByUser($this->em,1,1));
        $sign = new SignInForm();
        $sign->setFirstname('user');
        $sign->setEmail('mail2');
        $sign->setTelephone('1234');
        $sign->setLastname('test');
        $sign->setPassword('1234');
        $sign->setConfpassword('1234');
        $this->assertEquals('OK',UserData::insertUser($sign,$this->em));
        $this->assertCount(2,UserData::getAll($this->em));
        $sign = new ProjectForm();
        $sign->setBegin(new \DateTime('10/10/2016'));
        $sign->setName('project');
        $sign->setDescription('1234');
        $sign->setEnd(new \DateTime('12/12/2016'));
        ProjectData::addProject($this->em, 1, $sign);
        ProjectData::inviteUser($this->em,2,1,1);
        $this->assertCount(1, ProjectData::getAll($this->em));
        ProjectData::join($this->em, 2, 2);
        $this->assertEquals(true, NotationData::canNote($this->em,1,2,1));
    }

    public function testCanNoteHuman(){
        $this->testCanNote();
        $this->assertEquals(true, NotationData::canNoteHuman($this->em,1,2));
    }

    public function testCanNoteHumanFail(){
        $this->testCanNote();
        $this->assertEquals(true, NotationData::canNoteHuman($this->em,1,2));
        NotationData::addNotationHuman($this->em,1,2,3);
        $this->assertEquals(3,NotationData::getNotationForHuman($this->em,1));
        $this->assertEquals(false, NotationData::canNoteHuman($this->em,1,2));
    }

    public function testCanNoteFail(){
        $this->testCanNote();
        NotationData::addNotation($this->em,1,1,2,3);
        $this->assertEquals(3,NotationData::getAVGTech($this->em,1));
        $this->assertEquals(false, NotationData::canNote($this->em,1,2,1));
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
