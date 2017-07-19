<?php

namespace AppBundle\Controller;

use AppBundle\DataAccess\CommentData;
use AppBundle\DataAccess\NotationData;
use AppBundle\DataAccess\ProjectData;
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


class DefaultController extends Controller
{

    /**
     * @Route("/", name="index")
     */
    public function index(EntityManagerInterface $em, SessionInterface $session)
    {
        return $this->render('default/index.html.twig');
    }


    /**
     * @Route("/main", name="main")
     */
    public function mainAction(EntityManagerInterface $em, SessionInterface $session)
    {
        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');


        if (!UserData::isLogged($em, $session))
            return $this->redirectToRoute('log');

        else {
            return $this->render('default/main.html.twig', array(
                'id' => $id

            ));

        }

    }

    /**
     * @Route("/deconnect", name="deconnect")
     */
    public function deconnect(SessionInterface $session)
    {
        $session->set('uuid', '');
        return $this->redirectToRoute('log');
    }


    /**
     * @Route("/users/{slug}", name="displayUser")
     */
    public function displayUser($slug, Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        //todo:handle notation comment
        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');


        $sign = new CommentForm();
        $form = $this->createFormBuilder($sign)
            ->add('text', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Valider'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sign = $form->getData();
            $ret = CommentData::addComment($em, $slug, $id, $sign->getText());

            $sign->setText('');
            return $this->render('default/user.html.twig', array(
                'form' => $form->createView(),
                'user' => UserData::getUserByid($em, $slug),
                'ownedTech' => TechData::getTechByUser($em, $slug, $id),
                'ownedGroupe' => GroupeData::getGroupeByUser($em, $slug),
                'humanNote' => NotationData::getNotationForHuman($em, $slug),
                'comment' => CommentData::GetById($em, $slug),
                'project' => ProjectData::getProjectForUser($em, $id, $slug),
                'canNoteHuman' => NotationData::canNoteHuman($em, $slug, $id),
            ));

        }

        return $this->render('default/user.html.twig', array(
            'form' => $form->createView(),
            'user' => UserData::getUserByid($em, $slug),
            'ownedTech' => TechData::getTechByUser($em, $slug, $id),
            'ownedGroupe' => GroupeData::getGroupeByUser($em, $slug),
            'comment' => CommentData::GetById($em, $slug),
            'project' => ProjectData::getProjectForUser($em, $id, $slug),
            'canNoteHuman' => NotationData::canNoteHuman($em, $slug, $id),
            'humanNote' => NotationData::getNotationForHuman($em, $slug),
        ));
    }
}
