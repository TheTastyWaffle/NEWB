<?php

namespace Tests\AppBundle\DataAccess;

use AppBundle\DataAccess\CommentData;
use AppBundle\DataAccess\UserData;
use AppBundle\Entity\SignInForm;
use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CommentDataTest extends KernelTestCase{
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
        $sign = new SignInForm();
        $sign->setFirstname('user');
        $sign->setEmail('mail');
        $sign->setTelephone('1234');
        $sign->setLastname('test');
        $sign->setPassword('1234');
        $sign->setConfpassword('1234');
        UserData::insertUser($sign,$this->em);
        $sign2 = new SignInForm();
        $sign2->setFirstname('user2');
        $sign2->setEmail('mail2');
        $sign2->setTelephone('12342');
        $sign2->setLastname('test2');
        $sign2->setPassword('12342');
        $sign2->setConfpassword('12342');
        UserData::insertUser($sign2, $this->em);
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

    public function test_add_comment() {
        $products = UserData::getAll($this->em);
        $user1 = $products[0]->getIduser();
        $user2 = $products[1]->getIduser();
        CommentData::addComment($this->em, $user1, $user2, 'Hello my name is Brian and Im still evolving');
        $this->assertInstanceOf(Comment::class, CommentData::GetById($this->em, 1)[0]);
    }

    public function test_id_comment_neg() {
        $this->assertCount(0, CommentData::GetById($this->em, 1));
    }

    public function test_text_comnment() {
        $products = UserData::getAll($this->em);
        $user1 = $products[0]->getIduser();
        $user2 = $products[1]->getIduser();
        CommentData::addComment($this->em, $user1, $user2, 'Hello my name is Brian and Im still evolving');
        $this->assertEquals('Hello my name is Brian and Im still evolving', CommentData::GetById($this->em, 1)[0]->getText());
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
