<?php

namespace Tests\AppBundle\DataAccess;

use AppBundle\DataAccess\ProjectData;
use AppBundle\DataAccess\UserData;
use AppBundle\Entity\ProjectForm;
use AppBundle\Entity\SignInForm;
use AppBundle\Entity\Projet;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class DateGiver {
    public static function setDate() {
        return new DateTime('12/12/2017');
    }
}

class ProjectDataTest extends KernelTestCase{
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

    public function test_array_proj() {
        $this->assertEquals(Array(), ProjectData::getAll($this->em));
    }

    public function test_empty_proj() {
        $this->assertEmpty(ProjectData::getAll($this->em));
    }

    public function test_add_project() {
        $products = UserData::getAll($this->em);
        $sign = new ProjectForm();
        $sign->setBegin(new \DateTime('10/10/2017'));
        $sign->setName('project');
        $sign->setDescription('1234');
        $sign->setEnd(new \DateTime('12/12/2017'));
        ProjectData::addProject($this->em, $products[0]->getIduser(), $sign);
        $this->assertCount(1, ProjectData::getAll($this->em));

    }

    public function test_proj_id() {
        $products = UserData::getAll($this->em);
        $sign = new ProjectForm();
        $sign->setBegin(new \DateTime('10/10/2017'));
        $sign->setName('project');
        $sign->setDescription('1234');
        $sign->setEnd(new \DateTime('12/12/2017'));
        ProjectData::addProject($this->em, $products[0]->getIduser(), $sign);
        $allproj = ProjectData::getAll($this->em);
        $this->assertInstanceOf(Projet::class, ProjectData::getProjectById($this->em, $allproj[0]->getIdprojet()));
    }

    public function test_join() {
        $products = UserData::getAll($this->em);
        $sign = new ProjectForm();
        $sign->setBegin(new \DateTime('10/10/2017'));
        $sign->setName('project');
        $sign->setDescription('1234');
        $sign->setEnd(new \DateTime('12/12/2017'));
        ProjectData::addProject($this->em, $products[0]->getIduser(), $sign);
        $products = UserData::getAll($this->em);
        $allproj = ProjectData::getAll($this->em);
        $this->assertEquals(null, ProjectData::join($this->em, $products[0]->getIduser(), $allproj[0]->getIdprojet()));
    }

    public function test_delete_proj() {
        $products = UserData::getAll($this->em);
        $sign = new ProjectForm();
        $sign->setBegin(new \DateTime('10/10/2017'));
        $sign->setName('project');
        $sign->setDescription('1234');
        $sign->setEnd(new \DateTime('12/12/2017'));
        ProjectData::addProject($this->em, $products[0]->getIduser(), $sign);
        ProjectData::deleteProject($this->em, $products[0]->getIduser(), ProjectData::getAll($this->em)[0]->getIdprojet());
        $this->assertCount(0, ProjectData::getAll($this->em));
    }

    public function test_state_proj() {
        $products = UserData::getAll($this->em);
        $sign = new ProjectForm();
        $sign->setBegin(new \DateTime('10/10/2017'));
        $sign->setName('project');
        $sign->setDescription('1234');
        $sign->setEnd(new \DateTime('12/12/2017'));
        ProjectData::addProject($this->em, $products[0]->getIduser(), $sign);
        $this->assertNotEquals(4, ProjectData::getState($this->em, ProjectData::getAll($this->em)[0]->getIdprojet(),  $products[0]->getIduser()));
    }

    public function test_get_projectUser() {
        $products = UserData::getAll($this->em);
        $sign = new ProjectForm();
        $sign->setBegin(new \DateTime('10/10/2017'));
        $sign->setName('project');
        $sign->setDescription('1234');
        $sign->setEnd(new \DateTime('12/12/2017'));
        ProjectData::addProject($this->em, $products[0]->getIduser(), $sign);
        $products = UserData::getAll($this->em);
        $allproj = ProjectData::getAll($this->em);
        $this->assertCount(1, ProjectData::getProjectForUser($this->em, $products[0]->getIduser(), $products[0]->getIduser()));
    }

    public function test_ownage() {
        $products = UserData::getAll($this->em);
        $sign = new ProjectForm();
        $sign->setBegin(new \DateTime('10/10/2017'));
        $sign->setName('project');
        $sign->setDescription('1234');
        $sign->setEnd(new \DateTime('12/12/2017'));
        ProjectData::addProject($this->em, $products[0]->getIduser(), $sign);
        $products = UserData::getAll($this->em);
        $allproj = ProjectData::getAll($this->em);
        $this->assertEquals(true, ProjectData::ownProject($this->em, ProjectData::getAll($this->em)[0]->getIdprojet(), $products[0]->getIduser()));
    }

    public function test_is_in() {
        $products = UserData::getAll($this->em);
        $sign = new ProjectForm();
        $sign->setBegin(new \DateTime('10/10/2017'));
        $sign->setName('project');
        $sign->setDescription('1234');
        $sign->setEnd(new \DateTime('12/12/2017'));
        ProjectData::addProject($this->em, $products[0]->getIduser(), $sign);
        $products = UserData::getAll($this->em);
        $allproj = ProjectData::getAll($this->em);
        ProjectData::join($this->em, $products[0]->getIduser(), $allproj[0]->getIdprojet());
        $this->assertEquals(true, ProjectData::ownProject($this->em, ProjectData::getAll($this->em)[0]->getIdprojet(), $products[0]->getIduser()));
    }

    public function test_member() {
        $products = UserData::getAll($this->em);
        $sign = new ProjectForm();
        $sign->setBegin(new \DateTime('10/10/2017'));
        $sign->setName('project');
        $sign->setDescription('1234');
        $sign->setEnd(new \DateTime('12/12/2017'));
        ProjectData::addProject($this->em, $products[0]->getIduser(), $sign);
        $products = UserData::getAll($this->em);
        $allproj = ProjectData::getAll($this->em);
        ProjectData::join($this->em, $products[0]->getIduser(), $allproj[0]->getIdprojet());
        $this->assertCount(1, ProjectData::getProjectMemberById($this->em, ProjectData::getAll($this->em)[0]->getIdprojet()));
    }

    public function test_my_proj_mgl()
    {
        $products = UserData::getAll($this->em);
        $sign = new ProjectForm();
        $sign->setBegin(new \DateTime('10/10/2017'));
        $sign->setName('project');
        $sign->setDescription('1234');
        $sign->setEnd(new \DateTime('12/12/2017'));
        ProjectData::addProject($this->em, $products[0]->getIduser(), $sign);
        $sign2 = new ProjectForm();
        $sign2->setBegin(new \DateTime('10/10/2017'));
        $sign2->setName('project2');
        $sign2->setDescription('12342');
        $sign2->setEnd(new \DateTime('12/12/2017'));
        ProjectData::addProject($this->em, $products[0]->getIduser(), $sign2);
        $products = UserData::getAll($this->em);
        $allproj = ProjectData::getAll($this->em);
        $this->assertEquals(Array(), ProjectData::myProjects($this->em, $products[0]->getIduser()));
    }

    public function test_owned()
    {
        $products = UserData::getAll($this->em);
        $sign = new ProjectForm();
        $sign->setBegin(new \DateTime('10/10/2017'));
        $sign->setName('project');
        $sign->setDescription('1234');
        $sign->setEnd(new \DateTime('12/12/2017'));
        ProjectData::addProject($this->em, $products[0]->getIduser(), $sign);
        $sign2 = new ProjectForm();
        $sign2->setBegin(new \DateTime('10/10/2017'));
        $sign2->setName('project2');
        $sign2->setDescription('12342');
        $sign2->setEnd(new \DateTime('12/12/2017'));
        ProjectData::addProject($this->em, $products[0]->getIduser(), $sign2);
        $products = UserData::getAll($this->em);
        $allproj = ProjectData::getAll($this->em);
        $this->assertCount(2, ProjectData::owned($this->em, $products[0]->getIduser()));
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
