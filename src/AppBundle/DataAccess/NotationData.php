<?php

namespace AppBundle\DataAccess;

use AppBundle\Entity\NoteHuman;
use AppBundle\Entity\NoteTech;

use AppBundle\Entity\GroupeUser;
use AppBundle\Entity\Comment;
use AppBundle\Entity\LogInForm;
use AppBundle\Entity\TechUser;
use AppBundle\Entity\User;
use AppBundle\Entity\SignInForm;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class NotationData
{
    public static function addNotation(EntityManagerInterface $em, $iduser, $idtech, $idsender, $val)
    {
        $repository = $em->getRepository('AppBundle:TechUser');
        $product = $repository->findOneBy(
            array('iduser' => $iduser,
                'idtechno' => $idtech)
        );
        if (!$product)
            return;
        $note = new NoteTech();
        $note->setIdtechUser($product);
        $note->setNote($val);
        $note->setIdsender(UserData::getUserByid($em, $idsender));
        $em->persist($note);
        $em->flush();
    }


    public static function addNotationHuman(EntityManagerInterface $em, $iduser, $idsender, $val)
    {

        $sender = UserData::getUserByid($em, $idsender);
        $user = UserData::getUserByid($em, $iduser);

        $note = new NoteHuman();
        $note->setIduser($user);
        $note->setNote($val);
        $note->setIdsender($sender);
        $em->persist($note);
        $em->flush();
    }

    public static function getNotationForHuman(EntityManagerInterface $em, $iduser)
    {
        $queryBuilder = $em->createQueryBuilder();
        $product = $queryBuilder
            ->select($queryBuilder->expr()->avg('note.note'))
            ->from('AppBundle:NoteHuman', 'note')
            ->where('note.iduser = :id')
            ->setParameter('id', $iduser )
            ->groupBy('note.iduser')
            ->getQuery()
            ->getResult();
        if ($product)
            return $product[0][1];
    }

    public static function getNotationForTech(EntityManagerInterface $em, $idTech,$iduser)
    {
        $idtech_user = $em
            ->createQueryBuilder()
            ->select('techuser.idtechUser')
            ->from('AppBundle:TechUser', 'techuser')
            ->innerJoin('AppBundle:Techno', 'tech')
            ->where('techuser.idtechno = tech.idtechno')
            ->andWhere('techuser.iduser = :user_id')
            ->setParameter('user_id', $iduser)
            ->andWhere('tech.idtechno = :tech_id')
            ->setParameter('tech_id', $idTech)
            ->getQuery()
            ->getResult();

        if (!$idtech_user)
            return;
        $queryBuilder = $em->createQueryBuilder();
        $product = $queryBuilder
            ->select($queryBuilder->expr()->avg('note.note'))
            ->from('AppBundle:NoteTech', 'note')
            ->where('note.idtechUser = :id')
            ->setParameter('id', $idtech_user)
            ->groupBy('note.idtechUser')
            ->getQuery()
            ->getResult();

        if ($product) {
            return $product[0][1];
        }
    }

    public static function canNote(EntityManagerInterface $em, $iduser, $idowner, $tech)
    {
        $product = $em
            ->createQueryBuilder()
            ->select('projet')
            ->from('AppBundle:Projet', 'projet')
            ->andWhere('projet.idowner = :owner_id')
            ->setParameter('owner_id', $iduser)
            ->andWhere('projet.end < :date')
            ->setParameter('date', new \DateTime("now"))
            ->getQuery()
            ->getResult();
        $product2 = $em
            ->createQueryBuilder()
            ->select('projet')
            ->from('AppBundle:ProjetUser', 'projet')
            ->join('AppBundle:Projet', 'pro')
            ->where('pro.idprojet = projet.idprojet')
            ->andWhere('projet.iduser = :owner_id')
            ->setParameter('owner_id', $iduser)
            ->andWhere('pro.end < :date')
            ->setParameter('date', new \DateTime("now"))
            ->andWhere('projet.state = 0')
            ->getQuery()
            ->getResult();
        $stack = array();
        for ($i = 0; $i < count($product); $i++) {
            array_push($stack, $product[$i]);
        }
        for ($i = 0; $i < count($product2); $i++) {
            array_push($stack, $product2[$i]->getIdprojet());
        }
        $nEnd = 0;
        for ($i = 0; $i < count($stack); $i++) {
            $Memberlist = ProjectData::getProjectMemberById($em, $stack[$i]->getIdprojet());
            for ($j = 0; $j < count($Memberlist); $j++) {
                if ($Memberlist[$j]->getIduser() == $idowner)
                    $nEnd++;
            }
        }
        $repository2 = $em->getRepository('AppBundle:TechUser');
        $product3 = $repository2->findOneBy(
            array('iduser' => $iduser,
                'idtechno' => $tech,)
        );
        $repository2 = $em->getRepository('AppBundle:NoteTech');
        if(!$product3)
            return;
        $product2 = $repository2->findBy(
            array('idsender' => $idowner,
            'idtechUser' => $product3->getIdtechUser())
        );

        return ($nEnd ) > count($product2);
    }

    public static function getAVGTech(EntityManagerInterface $em, $iduser){
        $repository2 = $em->getRepository('AppBundle:TechUser');
        $product2 = $repository2->findBy(
            array('iduser' => $iduser,)
        );
        $res = 0;
        $n = 0;
        for ($i = 0; $i < count($product2); $i++) {
            $v = self::getNotationForTech($em,$product2[$i]->getIdtechno(),$iduser);
            if ($v)
            {
                $res += $v;
                $n++;
            }
        }
        if($n != 0)
            return $res/$n;
    }
    public static function canNoteHuman(EntityManagerInterface $em, $iduser, $idowner)
    {
        $product = $em
            ->createQueryBuilder()
            ->select('projet')
            ->from('AppBundle:Projet', 'projet')
            ->andWhere('projet.idowner = :owner_id')
            ->setParameter('owner_id', $iduser)
            ->andWhere('projet.end < :date')
            ->setParameter('date', new \DateTime("now"))
            ->getQuery()
            ->getResult();
        $product2 = $em
            ->createQueryBuilder()
            ->select('projet')
            ->from('AppBundle:ProjetUser', 'projet')
            ->join('AppBundle:Projet', 'pro')
            ->where('pro.idprojet = projet.idprojet')
            ->andWhere('projet.iduser = :owner_id')
            ->setParameter('owner_id', $iduser)
            ->andWhere('pro.end < :date')
            ->setParameter('date', new \DateTime("now"))
            ->andWhere('projet.state = 0')
            ->getQuery()
            ->getResult();
        $stack = array();
        for ($i = 0; $i < count($product); $i++) {
            array_push($stack, $product[$i]);
        }
        for ($i = 0; $i < count($product2); $i++) {
            array_push($stack, $product2[$i]->getIdprojet());
        }
        $nEnd = 0;
        for ($i = 0; $i < count($stack); $i++) {
            $Memberlist = ProjectData::getProjectMemberById($em, $stack[$i]->getIdprojet());
            for ($j = 0; $j < count($Memberlist); $j++) {
                if ($Memberlist[$j]->getIduser() == $idowner)
                    $nEnd++;
            }
        }
        $repository2 = $em->getRepository('AppBundle:NoteHuman');
        $product2 = $repository2->findBy(
            array('idsender' => $idowner)
        );
        return $nEnd > count($product2);
    }
}