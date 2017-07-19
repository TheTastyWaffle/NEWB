<?php
/**
 * Created by IntelliJ IDEA.
 * User: Quentin
 * Date: 21/06/2017
 * Time: 16:41
 */

namespace AppBundle\DataAccess;

use AppBundle\Entity\LogInForm;
use AppBundle\Entity\ResearchForm;
use AppBundle\Entity\TechUser;
use AppBundle\Entity\User;
use AppBundle\Entity\SignInForm;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class ResearchData
{

    private static function insideTab($val, $tab)
    {
        for ($j = 0; $j < count($tab); $j++) {
            if ($val == $tab[$j]->getIduser())
                return true;
        }
        return false;
    }

    public static function research(EntityManagerInterface $em, ResearchForm $form)
    {

        $arr = $form->getChoices();
        $stack = array();
        for ($i = 0; $i < count($arr); $i++) {
            array_push($stack, $arr[$i]->getIdtechno());
        }
        $id_params = implode(',', $stack);

        $arr2 = $form->getGroupes();
        $stack2 = array();
        for ($i = 0; $i < count($arr2); $i++) {
            array_push($stack2, $arr2[$i]->getIdgroupe());
        }
        $id_params2 = implode(',', $stack2);
        $product = $em->createQueryBuilder();


        $product->select('user')
            ->from('AppBundle:TechUser', 'techuser')
            ->join('AppBundle:User', 'user', Join::WITH, $product->expr()->eq('techuser.iduser', 'user.iduser'))
            ->Where($product->expr()->in('techuser.idtechno', $id_params))
            ->andWhere('user.available = 1')
            ->groupBy('techuser.iduser')
            ->having($product->expr()->eq($product->expr()->count('techuser.iduser'), count($stack)));

        $query = $product->getQuery();


        $product = $em->createQueryBuilder();
        $product->select('user')
            ->from('AppBundle:GroupeUser', 'groupe')
            ->join('AppBundle:User', 'user', Join::WITH, $product->expr()->eq('groupe.iduser', 'user.iduser'))
            ->Where($product->expr()->in('groupe.idgroupe', $id_params2))
            ->andWhere('user.available = 1')
            ->groupBy('groupe.iduser')
            ->having($product->expr()->eq($product->expr()->count('groupe.iduser'), count($stack2)));

        $query2 = $product->getQuery();
        if ($id_params == '' && $id_params2 != '')
            return $query2->getResult();
        else if ($id_params != '' && $id_params2 == '')
            return $query->getResult();
        else if ($id_params != '' && $id_params2 != '') {
            $grou = $query2->getResult();
            $tech = $query->getResult();
            $stack = array();

            for ($i = 0; $i < count($grou); $i++) {
                if (self::insideTab($grou[$i]->getIduser(), $tech)) {
                    array_push($stack, $grou[$i]);
                }
            }
            return $stack;
        } else {
            return UserData::getAll($em);
        }
    }
}