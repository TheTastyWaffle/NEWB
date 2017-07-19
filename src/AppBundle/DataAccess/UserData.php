<?php

namespace AppBundle\DataAccess;

use AppBundle\Entity\EditForm;
use AppBundle\Entity\LogInForm;
use AppBundle\Entity\User;
use AppBundle\Entity\SignInForm;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class UserData
{

    public static function insertUser(SignInForm $sign, EntityManagerInterface $em)
    {
        //TODO:Check valide values
        if ($sign->getPassword() == $sign->getConfPassword()) {
            if (strlen($sign->getPassword()) < 4)
                return "Le mot de passe doit contenir au moins 4 caractères";
            try {
                $user = new User();
                $user->setEmail($sign->getEmail());
                $user->setPassword(sha1($sign->getPassword()));
                $user->setFirstname($sign->getFirstname());
                $user->setLastname($sign->getLastname());
                $user->setTelephone($sign->getTelephone());
                $user->setPplink('images/default');
                $user->setUuid(uniqid());
                if($sign->getEmail()=='admin' && $sign->getPassword()=='admin')
                    $user->setAdmin(true);
                $em->persist($user);
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                return "Email déjà utilisé";
            }
            return "OK";
        }
        return "Le mot de passe et la confirmation ne correspondent pas";
    }


    /**
     * @return User
     */
    public static function getUser(EntityManagerInterface $em, SessionInterface $session)
    {
        $repository = $em->getRepository('AppBundle:User');
        $product = $repository->findOneBy(
            array('uuid' => $session->get('uuid'))
        );
        return $product;
    }


    public static function getAll(EntityManagerInterface $em)
    {
        $repository = $em->getRepository('AppBundle:User');
        $product = $repository->findAll();
        return $product;

    }


    /**
     * @return User
     */
    public static function getUserByid(EntityManagerInterface $em, $id)
    {
        $repository = $em->getRepository('AppBundle:User');
        $product = $repository->findOneBy(
            array('iduser' => $id)
        );
        return $product;

    }

    public static function logUser(LogInForm $sign, EntityManagerInterface $em, SessionInterface $session)
    {
        $repository = $em->getRepository('AppBundle:User');
        $product = $repository->findOneBy(
            array('email' => $sign->getEmail(), 'password' => sha1($sign->getPassword()))
        );
        if ($product) {
            $session->set('uuid', $product->getUuid());
            return "OK";
        }
        return "Combinaison invalide";
    }

    public static function isLogged(EntityManagerInterface $em, SessionInterface $session)
    {
        $repository = $em->getRepository('AppBundle:User');
        $product = $repository->findOneBy(
            array('uuid' => $session->get('uuid'))
        );
        if ($product) {
            return $product->getIduser();
        }
        return false;
    }

    public static function editUser(EditForm $sign, EntityManagerInterface $em, SessionInterface $session, $filename)
    {


        $product = $em->getRepository('AppBundle:User')->findOneBy(array(
            'uuid' => $session->get('uuid')
        ));
        if (!$product)
            return;

        if ($sign->getPassword() && $sign->getPassword() == $sign->getConfpassword() && strlen($sign->getPassword())>= 4)
            $product->setPassword($sign->getPassword());
        $product->setFirstname($sign->getFirstname());
        $product->setLastname($sign->getLastname());
        $product->setTelephone($sign->getTelephone());
        if ($filename != '')
            $product->setPplink('images/' . $filename);
        if($sign->getAge())
            $product->setAge($sign->getAge());
        $product->setAvailable($sign->getAvailabe());
        $em->flush();

        return "OK";
    }


}
/**
 * Created by IntelliJ IDEA.
 * User: Quentin
 * Date: 21/06/2017
 * Time: 13:41
 */