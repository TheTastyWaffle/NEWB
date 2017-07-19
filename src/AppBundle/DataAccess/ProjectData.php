<?php

namespace AppBundle\DataAccess;

use AppBundle\Entity\NoteTech;

use AppBundle\Entity\GroupeUser;
use AppBundle\Entity\Comment;
use AppBundle\Entity\LogInForm;
use AppBundle\Entity\ProjectForm;
use AppBundle\Entity\Projet;
use AppBundle\Entity\ProjetUser;
use AppBundle\Entity\TechUser;
use AppBundle\Entity\User;
use AppBundle\Entity\SignInForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Ldap\Adapter\EntryManagerInterface;


class ProjectData
{

    public static function addProject(EntityManagerInterface $em, $id, ProjectForm $form)
    {
        $repository = $em->getRepository('AppBundle:User');
        $product = $repository->findOneBy(
            array('iduser' => $id)
        );
        if (!$product)
            return;
        $proj = new Projet();
        $proj->setIdowner($product);
        $proj->setBegin($form->getBegin());
        $proj->setDescription($form->getDescription());
        $proj->setName($form->getName());
        $proj->setEnd($form->getEnd());
        $em->persist($proj);
        $em->flush();
    }

    public static function inviteUser(EntityManagerInterface $em, $iduser, $idproject, $idowner)
    {

        $repository2 = $em->getRepository('AppBundle:ProjetUser');
        $product = $repository2->findOneBy(
            array('idprojet' => $idproject,
                'iduser' => $iduser)
        );
        if ($product)
            return;
        $repository2 = $em->getRepository('AppBundle:Projet');
        $product = $repository2->findOneBy(
            array('idprojet' => $idproject,
                'idowner' => $iduser)
        );
        if ($product)
            return;

        $repository = $em->getRepository('AppBundle:User');
        $user = $repository->findOneBy(
            array('iduser' => $iduser)
        );
        if (!$user)
            return;
        $repository2 = $em->getRepository('AppBundle:Projet');
        $product = $repository2->findOneBy(
            array('idprojet' => $idproject)
        );

        if (!$product)
            return;

        $repository = $em->getRepository('AppBundle:User');
        $owner = $repository->findOneBy(
            array('iduser' => $idowner)
        );

        $projetUser = new ProjetUser();
        $projetUser->setIduser($user);
        $projetUser->setDate(new \DateTime("now"));
        $projetUser->setIdprojet($product);
        $projetUser->setText('Email : '.($owner->getEmail()).' Numero de telephone : '.($owner->getTelephone()));
        /*
         * States 0 : Joined
         *        1 : pending
         */
        $projetUser->setState(1);
        $em->persist($projetUser);
        $em->flush();
    }


    public static function join(EntityManagerInterface $em, $iduser, $idprojet)
    {
        $repository = $em->getRepository('AppBundle:ProjetUser');
        $product = $repository->findOneBy(
            array('iduser' => $iduser,
                'idprojet' => $idprojet)
        );
        if (!$product)
            return;
        $product->setState(0);
        $em->flush();
    }

    public static function leaveProject(EntityManagerInterface $em, $iduser, $idprojet)
    {
        $repository = $em->getRepository('AppBundle:ProjetUser');
        $product = $repository->findOneBy(
            array('iduser' => $iduser,
                'idprojet' => $idprojet)
        );
        if (!$product)
            return;
        $em->remove($product);
        $em->flush();
    }

    public static function deleteProject(EntityManagerInterface $em, $idprojet)
    {
        $repository = $em->getRepository('AppBundle:ProjetUser');
        $arr = $repository->findBy(
            array('idprojet' => $idprojet)
        );
        for ($j = 0; $j < count($arr); $j++) {
            $em->remove($arr[$j]);

        }
        $repository2 = $em->getRepository('AppBundle:Projet');
        $product = $repository2->findOneBy(
            array('idprojet' => $idprojet)
        );
        if (!$product)
            return;
        $em->remove($product);
        $em->flush();
    }

    public static function getAll(EntityManagerInterface $em)
    {
        $repository = $em->getRepository('AppBundle:Projet');
        $product = $repository->findAll();
        //print_r($product);
        return $product;
    }

    public static function getState(EntityManagerInterface $em, $idProject, $id)
    {
        $project = self::getProjectById($em, $idProject);
        if (!$project)
            return 4;
        if ($project->getIdowner()->getIduser() == $id) {
            return 2;
        }
        $arr = $em
            ->createQueryBuilder()
            ->select('projetuser')
            ->from('AppBundle:Projet', 'projet')
            ->innerJoin('AppBundle:ProjetUser', 'projetuser')
            ->where('projet.idprojet = projetuser.idprojet')
            ->andWhere('projetuser.iduser = :owner_id')
            ->setParameter('owner_id', $id)
            ->andWhere('projetuser.idprojet = :projet_id')
            ->setParameter('projet_id', $idProject)
            ->getQuery()
            ->getResult();
        if (count($arr) < 1) {
            return 3;
        } else {
            return $arr[0]->getState();
        }

    }

    private static function insideTab($val, $tab)
    {
        for ($j = 0; $j < count($tab); $j++) {
            if ($val == $tab[$j]->getIdprojet()->getIdprojet())
                return true;
        }
        return false;
    }

    public static function getProjectForUser(EntityManagerInterface $em, $idowner, $iduser)
    {
        $arr = $em
            ->createQueryBuilder()
            ->select('projet')
            ->from('AppBundle:Projet', 'projet')
            ->Where('projet.idowner = :owner_id')
            ->setParameter('owner_id', $idowner)
            ->andWhere('projet.end > :date')
            ->setParameter('date', new \DateTime("now"))
            ->getQuery()
            ->getResult();


        $repository = $em->getRepository('AppBundle:ProjetUser');
        $product = $repository->findBy(
            array('iduser' => $iduser)
        );

        $stack = array();
        for ($i = 0; $i < count($arr); $i++) {
            if (!self::insideTab($arr[$i]->getIdprojet(), $product))
                array_push($stack, $arr[$i]);
        }
        return $stack;

    }

    public static function getProjectById(EntityManagerInterface $em, $idProject)
    {
        $repository2 = $em->getRepository('AppBundle:Projet');
        $product = $repository2->findOneBy(
            array('idprojet' => $idProject)
        );
        return $product;
    }

    public static function ownProject(EntityManagerInterface $em, $idprojet, $id)
    {
        $repository2 = $em->getRepository('AppBundle:Projet');
        $product = $repository2->findOneBy(
            array('idprojet' => $idprojet,
                'idowner' => $id)
        );
        if ($product)
            return true;
        return false;
    }

    public static function isInProject(EntityManagerInterface $em, $idprojet, $id)
    {
        $repository2 = $em->getRepository('AppBundle:Projet');
        $product = $repository2->findOneBy(
            array('idprojet' => $idprojet,
                'idowner' => $id)
        );
        if ($product)
            return true;
        $repository2 = $em->getRepository('AppBundle:ProjetUser');
        $product = $repository2->findOneBy(
            array('idprojet' => $idprojet,
                'iduser' => $id)
        );
        if ($product)
            return true;
        return false;
    }


    public static function getProjectMemberById(EntityManagerInterface $em, $idProject)
    {
        $repository = $em->getRepository('AppBundle:Projet');
        $product = $repository->findOneBy(
            array('idprojet' => $idProject)
        );
        $repository2 = $em->getRepository('AppBundle:ProjetUser');
        $product2 = $repository2->findBy(
            array('idprojet' => $idProject)
        );

        $stack = array();
        array_push($stack, $product->getIdowner());
        for ($i = 0; $i < count($product2); $i++) {
            array_push($stack, $product2[$i]->getIduser());
        }
        return $stack;
    }

    public static function myInvitations(EntityManagerInterface $em, $idowner)
    {
        $arr = $em
            ->createQueryBuilder()
            ->select('projet')
            ->from('AppBundle:Projet', 'projet')
            ->innerJoin('AppBundle:ProjetUser', 'projetuser')
            ->where('projet.idprojet = projetuser.idprojet')
            ->andWhere('projetuser.iduser = :owner_id')
            ->setParameter('owner_id', $idowner)
            ->andWhere('projet.end > :date')
            ->setParameter('date', new \DateTime("now"))
            ->andWhere('projetuser.state = 1')
            ->getQuery()
            ->getResult();
        for ($i = 0; $i < count($arr); $i++) {
            $repository = $em->getRepository('AppBundle:ProjetUser');
            $product = $repository->findOneBy(
                array('idprojet' => $arr[$i]->getIdprojet(),
                    'iduser' => $idowner)
            );
            $arr[$i]->text = $product->getText();
        }
        return $arr;
    }

    public static function myProjects(EntityManagerInterface $em, $idowner)
    {
        $arr = $em
            ->createQueryBuilder()
            ->select('projet')
            ->from('AppBundle:Projet', 'projet')
            ->innerJoin('AppBundle:ProjetUser', 'projetuser')
            ->where('projet.idprojet = projetuser.idprojet')
            ->andWhere('projetuser.iduser = :owner_id')
            ->setParameter('owner_id', $idowner)
            ->andWhere('projet.end > :date')
            ->setParameter('date', new \DateTime("now"))
            ->andWhere('projetuser.state = 0')
            ->getQuery()
            ->getResult();
        return $arr;
    }

    public static function owned(EntityManagerInterface $em, $idowner)
    {
        $arr = $em
            ->createQueryBuilder()
            ->select('projet')
            ->from('AppBundle:Projet', 'projet')
            ->andWhere('projet.idowner = :owner_id')
            ->setParameter('owner_id', $idowner)
            ->andWhere('projet.end > :date')
            ->setParameter('date', new \DateTime("now"))
            ->getQuery()
            ->getResult();
        return $arr;
    }
}
