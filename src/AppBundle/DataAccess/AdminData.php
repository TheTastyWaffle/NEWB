<?php
/**
 * Created by IntelliJ IDEA.
 * User: Quentin
 * Date: 21/06/2017
 * Time: 16:41
 */

namespace AppBundle\DataAccess;

use AppBundle\Entity\Groupe;
use AppBundle\Entity\LogInForm;
use AppBundle\Entity\Techno;
use AppBundle\Entity\TechUser;
use AppBundle\Entity\User;
use AppBundle\Entity\SignInForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class AdminData
{
    public static function isAdmin(EntityManagerInterface $em, SessionInterface $session){
        $repository = $em->getRepository('AppBundle:User');
        $product = $repository->findOneBy(
            array('uuid' => $session->get('uuid'),
                'admin' => 1)
        );
        if ($product) {
            return $product->getIduser();
        }
        return false;
    }

    public static function addAdmin(EntityManagerInterface $em, $id){
        $product = $em->getRepository('AppBundle:User')
            ->findOneBy(array('iduser' => $id));
        if (!$product)
            return;
        $product->setAdmin(1);
        $em->flush();

    }

    public static function removeAdmin(EntityManagerInterface $em, $id){
        $product = $em->getRepository('AppBundle:User')
            ->findOneBy(array('iduser' => $id));
        if (!$product)
            return;
        $product->setAdmin(0);
        $em->flush();
    }

    public static function addTech(EntityManagerInterface $em, LogInForm $form){
        $comment = new Techno();
        $comment->setName($form->getPassword());
        $em->persist($comment);
        $em->flush();
    }

    public static function addGroupe(EntityManagerInterface $em, LogInForm $form){
        $comment = new Groupe();
        $comment->setName($form->getEmail());
        $em->persist($comment);
        $em->flush();
    }

}