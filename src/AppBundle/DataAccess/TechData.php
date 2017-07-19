<?php
/**
 * Created by IntelliJ IDEA.
 * User: Quentin
 * Date: 21/06/2017
 * Time: 16:41
 */

namespace AppBundle\DataAccess;

use AppBundle\Entity\LogInForm;
use AppBundle\Entity\TechUser;
use AppBundle\Entity\User;
use AppBundle\Entity\SignInForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class TechData
{
    public static function getAll(EntityManagerInterface $em)
    {
        $repository = $em->getRepository('AppBundle:Techno');
        $product = $repository->findAll();
        return $product;
    }

    public static function getTechByUser(EntityManagerInterface $em, $id,$idsender)
    {
        $product = $em
            ->createQueryBuilder()
            ->select('tech')
            ->from('AppBundle:TechUser', 'techuser')
            ->innerJoin('AppBundle:Techno', 'tech')
            ->where('techuser.idtechno = tech.idtechno')
            ->andWhere('techuser.iduser = :user_id')
            ->setParameter('user_id', $id)
            ->getQuery()
            ->getResult();
        for ($j = 0; $j < count($product); $j++) {
            $product[$j]->note = NotationData::getNotationForTech($em, $product[$j]->getIdTechno(), $id);
            if($idsender != 0)
                $product[$j]->canNote = NotationData::canNote($em,$id,$idsender,$product[$j]->getIdTechno());

        }
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

    public static function getTechNotOwnedByUser(EntityManagerInterface $em, $id)
    {
        $arr = self::getAll($em);
        $f = self::getTechByUser($em, $id,0);
        $stack = array();
        for ($i = 0; $i < count($arr); $i++) {
            if (!self::insideTab($arr[$i]->getName(), $f))
                array_push($stack, $arr[$i]);
        }
        return $stack;
    }

    public static function addToUser(EntityManagerInterface $em, $id, $idTech)
    {
        $repository = $em->getRepository('AppBundle:TechUser');
        $product = $repository->findOneBy(
            array('iduser' => $id,
                'idtechno' => $idTech)
        );
        if ($product)
            return;

        $repository = $em->getRepository('AppBundle:Techno');
        $techno = $repository->findOneBy(
            array('idtechno' => $idTech)
        );
        if (!$techno)
            return;
        $repository2 = $em->getRepository('AppBundle:User');
        $user = $repository2->findOneBy(
            array('iduser' => $id)
        );
        if (!$user)
            return;
        //TODO:Check valide values

        $tech = new TechUser();
        $tech->setIdtechno($techno);
        $tech->setIduser($user);
        $em->persist($tech);
        $em->flush();

    }

    public static function removeFromUser(EntityManagerInterface $em, $id, $idTech)
    {
        $repository = $em->getRepository('AppBundle:TechUser');
        $product = $repository->findOneBy(
            array('iduser' => $id,
                'idtechno' => $idTech)
        );
        if (!$product)
            return;
        $em->remove($product);
        $em->flush();
        return "Password doesn't match";
    }
}