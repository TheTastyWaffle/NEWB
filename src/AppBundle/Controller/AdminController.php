<?php

namespace AppBundle\Controller;

use AppBundle\DataAccess\AdminData;
use AppBundle\DataAccess\CommentData;
use AppBundle\DataAccess\NotationData;
use AppBundle\DataAccess\ProjectData;
use AppBundle\DataAccess\TechData;
use AppBundle\DataAccess\GroupeData;
use AppBundle\DataAccess\UserData;
use AppBundle\Entity\CommentForm;
use AppBundle\Entity\LogInForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin(EntityManagerInterface $em, Request $request, SessionInterface $session)
    {
        if (!AdminData::isAdmin($em, $session))
            return $this->redirectToRoute('deconnect');
        $sign = new LogInForm();
        $form = $this->createFormBuilder($sign)
            ->add('password', TextType::class, array('label' => 'Techno'))
            ->add('save', SubmitType::class, array('label' => 'Ajouter'))
            ->getForm();


        $sign2 = new LogInForm();
        $form2 = $this->createFormBuilder($sign2)
            ->add('email', TextType::class, array('label' => 'Groupe'))
            ->add('save', SubmitType::class, array('label' => 'Ajouter'))
            ->getForm();


        /*
         * Really ugly
         */
        $res1 = $this->createFormBuilder($sign2)
            ->add('email', TextType::class, array('label' => 'Projet'))
            ->add('password', TextType::class, array('label' => 'Projet'))
            ->add('save', SubmitType::class, array('label' => 'Ajouter'))
            ->getForm();
        $res1->handleRequest($request);
        if ($res1->isSubmitted() && $res1->isValid()) {
            $res = $res1->getData();
            if ($res->getPassword() != '') {
                AdminData::addTech($em, $res);
            }
            if ($res->getEmail() != '') {
                AdminData::addGroupe($em, $res);
            }

        }
        return $this->render('default/adminMain.html.twig', array(
            'users' => UserData::getAll($em),
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ));

    }

    /**
     * @Route("/admin/add/{slug}", name="AddAdmin")
     */
    public function addAdmin($slug, Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if (!AdminData::isAdmin($em, $session))
            return $this->redirectToRoute('deconnect');
        AdminData::addAdmin($em, $slug);
        return $this->redirectToRoute('admin');


    }

    /**
     * @Route("/admin/remove/{slug}", name="removeAdmin")
     */
    public function removeAdmin($slug, Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if (!AdminData::isAdmin($em, $session))
            return $this->redirectToRoute('deconnect');
        AdminData::removeAdmin($em, $slug);
        return $this->redirectToRoute('admin');


        // ...
    }
}
