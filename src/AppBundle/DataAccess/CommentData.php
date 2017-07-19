<?php

namespace AppBundle\DataAccess;

use AppBundle\Entity\GroupeUser;
use AppBundle\Entity\Comment;
use AppBundle\Entity\LogInForm;
use AppBundle\Entity\TechUser;
use AppBundle\Entity\User;
use AppBundle\Entity\SignInForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class CommentData
{
    public static function GetById(EntityManagerInterface $em, $id)
    {
        $repository = $em->getRepository('AppBundle:Comment');
        $product = $repository->findBy(
            array('idreciever' => $id)
        );

        return $product;
    }

    public static function addComment(EntityManagerInterface $em, $idreciever, $idsender, $text)
    {
        $comment = new Comment();
        $comment->setIdreciever(UserData::getUserByid($em, $idreciever));
        $comment->setIdsender(UserData::getUserByid($em, $idsender));
        $comment->setDate(new \DateTime("now"));
        $comment->setText($text);
        $em->persist($comment);
        $em->flush();
    }


}