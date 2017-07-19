<?php

namespace AppBundle\Controller;

use AppBundle\DataAccess\CommentData;
use AppBundle\DataAccess\NotationData;
use AppBundle\DataAccess\TechData;
use AppBundle\DataAccess\GroupeData;
use AppBundle\DataAccess\UserData;
use AppBundle\Entity\CommentForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class NotationController extends Controller
{
    /**
     * @Route("/notation/{tech}/{user}/{note}", name="noteTech")
     */
    public function noteUser($tech, $user, $note, EntityManagerInterface $em, SessionInterface $session)
    {

        //Todo: check if "canNote OK"

        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');
        if (!NotationData::canNote($em, $user, $id, $tech))
            return $this->redirectToRoute('displayUser', array('slug' => $user));
        NotationData::addNotation($em, $user, $tech, $id, $note);
        return $this->redirectToRoute('displayUser', array('slug' => $user));
    }

    /**
     * @Route("/notatation/human/{user}/{note}", name="noteHuman")
     */
    public function noteHuman($user, $note, EntityManagerInterface $em, SessionInterface $session)
    {
        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');
        if (!NotationData::canNoteHuman($em, $user, $id))
            return $this->redirectToRoute('displayUser', array('slug' => $user));
        NotationData::addNotationHuman($em, $user, $id, $note);
        return $this->redirectToRoute('displayUser', array('slug' => $user));
    }
}