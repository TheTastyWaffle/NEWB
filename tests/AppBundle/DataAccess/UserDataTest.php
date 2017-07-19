<?php

namespace Tests\AppBundle\DataAccess;

use AppBundle\DataAccess\UserData;
use AppBundle\Entity\LogInForm;
use AppBundle\Entity\SignInForm;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class UserDataTest extends KernelTestCase{
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

    public function testInsertUserOK()
    {
        $sign = new SignInForm();
        $sign->setFirstname('user');
        $sign->setEmail('mail');
        $sign->setTelephone('1234');
        $sign->setLastname('test');
        $sign->setPassword('1234');
        $sign->setConfpassword('1234');
        $this->assertEquals('OK',UserData::insertUser($sign,$this->em));
        $products = UserData::getAll($this->em);
        $this->assertCount(1, $products);
    }
    public function testInsertUserPasswordMatchFail()
    {
        $sign = new SignInForm();
        $sign->setFirstname('user');
        $sign->setEmail('mail');
        $sign->setTelephone('1234');
        $sign->setLastname('test');
        $sign->setPassword('1234');
        $sign->setConfpassword('12344');
        $this->assertEquals('Le mot de passe et la confirmation ne correspondent pas',UserData::insertUser($sign,$this->em));
        $products = UserData::getAll($this->em);
        $this->assertCount(0, $products);
    }
    public function testInsertUserPasswordlengthFail()
    {
        $sign = new SignInForm();
        $sign->setFirstname('user');
        $sign->setEmail('mail');
        $sign->setTelephone('1234');
        $sign->setLastname('test');
        $sign->setPassword('123');
        $sign->setConfpassword('123');
        $this->assertEquals('Le mot de passe doit contenir au moins 4 caractères',UserData::insertUser($sign,$this->em));
        $products = UserData::getAll($this->em);
        $this->assertCount(0, $products);
    }

    public function testInsertUserSameMailFail()
    {
        $sign = new SignInForm();
        $sign->setFirstname('user');
        $sign->setEmail('mail');
        $sign->setTelephone('1234');
        $sign->setLastname('test');
        $sign->setPassword('1234');
        $sign->setConfpassword('1234');
        $this->assertEquals('OK',UserData::insertUser($sign,$this->em));
        $this->assertEquals('Email déjà utilisé',UserData::insertUser($sign,$this->em));
        $products = UserData::getAll($this->em);
        $this->assertCount(1, $products);
    }

    public function testLogInOK(){
        $this->testInsertUserOK();
        $log = new LogInForm();
        $log->setPassword('1234');
        $log->setEmail('mail');
        $this->assertEquals('OK',UserData::logUser($log,$this->em,$this->session));
    }
    public function testLogInFail(){
        $log = new LogInForm();
        $log->setPassword('fail');
        $log->setEmail('fail');
        $this->assertEquals('Combinaison invalide',UserData::logUser($log,$this->em,$this->session));
    }

    public function testIsLogged(){
        $this->testLogInOK();
        $this->assertEquals(1,UserData::isLogged($this->em,$this->session));
    }

    public function testIsLoggedFail(){
        $this->testLogInFail();
        $this->assertEquals(false,UserData::isLogged($this->em,$this->session));
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