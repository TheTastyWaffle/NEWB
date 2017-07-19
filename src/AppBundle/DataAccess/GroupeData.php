<?php
/**
 * Created by IntelliJ IDEA.
 * User: Quentin
 * Date: 21/06/2017
 * Time: 16:41
 */

namespace AppBundle\DataAccess;

use AppBundle\Entity\GroupeUser;
use AppBundle\Entity\LogInForm;
use AppBundle\Entity\TechUser;
use AppBundle\Entity\User;
use AppBundle\Entity\SignInForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class GroupeData
{
    public static function getAll(EntityManagerInterface $em)
    {
        $repository = $em->getRepository('AppBundle:Groupe');
        $product = $repository->findAll();
        return $product;
    }

    public static function getGroupeByUser(EntityManagerInterface $em, $id)
    {
        $product = $em
            ->createQueryBuilder()
            ->select('groupe')
            ->from('AppBundle:GroupeUser', 'groupeuser')
            ->innerJoin('AppBundle:Groupe', 'groupe')
            ->where('groupeuser.idgroupe = groupe.idgroupe')
            ->andWhere('groupeuser.iduser = :user_id')
            ->setParameter('user_id', $id)
            ->getQuery()
            ->getResult();
        return $product;
    }


    private static function insideTab($val, $tab)
    {
        for ($j = 0; $j < count($tab); $j++) {
            if ($val == $tab[$j]->getName())
                return true;
        }
        return false;
    }

    public static function getGroupeNotOwnedByUser(EntityManagerInterface $em, $id)
    {

        $arr = self::getAll($em);
        $f = self::getGroupeByUser($em, $id);

        $stack = array();

        for ($i = 0; $i < count($arr); $i++) {
            if (!self::insideTab($arr[$i]->getName(), $f))
                array_push($stack, $arr[$i]);
        }
        return $stack;
    }

    public static function addToUser(EntityManagerInterface $em, $id, $idgroupe)
    {

        $repository = $em->getRepository('AppBundle:GroupeUser');
        $product = $repository->findOneBy(
            array('iduser' => $id,
                'idgroupe' => $idgroupe)
        );
        if ($product)
            return;
        $repository = $em->getRepository('AppBundle:Groupe');
        $groupeobj = $repository->findOneBy(
            array('idgroupe' => $idgroupe)
        );
        if (!$groupeobj)
            return;
        $repository2 = $em->getRepository('AppBundle:User');
        $user = $repository2->findOneBy(
            array('iduser' => $id)
        );
        if (!$user)
            return;
        //TODO:Check valide values
        $groupe = new GroupeUser();
        $groupe->setIdgroupe($groupeobj);
        $groupe->setIduser($user);
        $em->persist($groupe);
        $em->flush();

    }

    public static function removeFromUser(EntityManagerInterface $em, $id, $idGroupe)
    {
        $repository = $em->getRepository('AppBundle:GroupeUser');
        $product = $repository->findOneBy(
            array('iduser' => $id,
                'idgroupe' => $idGroupe)
        );
        if (!$product)
            return;
        $em->remove($product);
        $em->flush();

        return "Password doesn't match";
    }

}