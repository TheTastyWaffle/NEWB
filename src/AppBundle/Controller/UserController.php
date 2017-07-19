<?php

namespace AppBundle\Controller;


use AppBundle\DataAccess\GroupeData;
use AppBundle\DataAccess\TechData;
use AppBundle\DataAccess\UserData;
use AppBundle\Entity\EditForm;
use AppBundle\Entity\GroupeUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\SignInForm;
use AppBundle\Entity\LogInForm;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class UserController extends Controller
{


    /**
     * @Route("/signIn", name="sign")
     */
    public function signIn(Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if (UserData::isLogged($em, $session))
            return $this->redirectToRoute('main');
        // create a task and give it some dummy data for this example
        $sign = new SignInForm();
        $form = $this->createFormBuilder($sign)
            ->add('email', TextType::class)
            ->add('password', PasswordType::class, array('label' => 'Mot de passe'))
            ->add('confpassword', PasswordType::class, array('label' => 'Confirmer le mot de passe'))
            ->add('lastname', TextType::class, array('label' => 'Nom'))
            ->add('firstname', TextType::class, array('label' => 'Prenom'))
            ->add('telephone', TextType::class, array('label' => 'Telephone'))
            ->add('save', SubmitType::class, array('label' => 'Valider'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sign = $form->getData();
            $ret = UserData::insertUser($sign, $em);

            if ($ret == "OK") {
                return $this->redirectToRoute('log');
            }
            return $this->render('default/signIn.html.twig', array(
                'form' => $form->createView(),
                'err' => $ret,
            ));

        }

        return $this->render('default/signIn.html.twig', array(
            'form' => $form->createView(),
            'err' => '',
        ));
    }

    /**
     * @Route("/logIn", name="log")
     */
    public function logIn(Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if (UserData::isLogged($em, $session))
            return $this->redirectToRoute('main');

        $sign = new LogInForm();
        $form = $this->createFormBuilder($sign)
            ->add('email', TextType::class)
            ->add('password', PasswordType::class, array('label' => 'Mot de passe'))
            ->add('save', SubmitType::class, array('label' => 'Valider'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sign = $form->getData();
            $ret = UserData::logUser($sign, $em, $session);

            if ($ret == "OK") {
                return $this->redirectToRoute('main');
            }
            return $this->render('default/signIn.html.twig', array(
                'form' => $form->createView(),
                'err' => $ret,
            ));

        }
        return $this->render('default/signIn.html.twig', array(
            'form' => $form->createView(),
            'err' => '',
        ));
    }

    /**
     * @Route("/user/edit", name="userEdit")
     */
    public function EditUser(Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');

        $user = UserData::getUser($em, $session);
        $sign = new EditForm();
        $sign->setTelephone($user->getTelephone());
        $sign->setLastname($user->getLastname());
        $sign->setFirstname($user->getFirstname());
        $sign->setAge($user->getAge());
        $sign->setAvailabe($user->getAvailable());
        $form = $this->createFormBuilder($sign)
            ->add('password', PasswordType::class, array('label' => 'Mot de passe:', 'required' => false))
            ->add('confpassword', PasswordType::class, array('label' => 'Confirmer le mot de passe:', 'required' => false))
            ->add('lastname', TextType::class, array('label' => 'Nom:'))
            ->add('firstname', TextType::class, array('label' => 'Prenom:'))
            ->add('telephone', TextType::class, array('label' => 'Telephone:'))
            ->add('age', NumberType::class, array('label' => 'Age:', 'required' => false))
            ->add('availabe', CheckboxType::class, array('label' => 'Disponible:', 'required' => false))
            ->add('file', FileType::class, array('label' => 'Modifier image profil:', 'required' => false))
            ->add('save', SubmitType::class, array('label' => 'Valider:'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sign = $form->getData();
            $file = $sign->getFile();
            if ($file) {

                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } else
                $fileName = '';
            $ret = UserData::editUser($sign, $em, $session, $fileName);


            return $this->render('default/edit.html.twig', array(
                'form' => $form->createView(),
                'err' => $ret,
                'img' => $user->getPplink(),
                'ownedTech' => TechData::getTechByUser($em, $id, 0),
                'availabletech' => TechData::getTechNotOwnedByUser($em, $id),
                'ownedGroupe' => GroupeData::getGroupeByUser($em, $id),
                'availableGroupe' => GroupeData::getGroupeNotOwnedByUser($em, $id),
            ));

        }

        return $this->render('default/edit.html.twig', array(
            'form' => $form->createView(),
            'err' => '',
            'img' => $user->getPplink(),
            'ownedTech' => TechData::getTechByUser($em, $id, 0),
            'availabletech' => TechData::getTechNotOwnedByUser($em, $id),
            'ownedGroupe' => GroupeData::getGroupeByUser($em, $id),
            'availableGroupe' => GroupeData::getGroupeNotOwnedByUser($em, $id),
        ));
    }

    //TODO:check on groupe n tech

    /**
     * @Route("/user/addTech/{slug}", name="addTech")
     */
    public function addTech($slug, Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');
        TechData::addToUser($em, $id, $slug);
        return $this->redirectToRoute('userEdit');


        // ...
    }

    /**
     * @Route("/user/removeTech/{slug}", name="removeTech")
     */
    public function removeTech($slug, Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');
        TechData::removeFromUser($em, $id, $slug);
        return $this->redirectToRoute('userEdit');


        // ...
    }

    /**
     * @Route("/user/addGroupe/{slug}", name="addGroupe")
     */
    public function addGroupe($slug, Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');
        GroupeData::addToUser($em, $id, $slug);
        return $this->redirectToRoute('userEdit');


        // ...
    }

    /**
     * @Route("/user/removeGroupe/{slug}", name="removeGroupe")
     */
    public function removeGroupe($slug, Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');
        GroupeData::removeFromUser($em, $id, $slug);
        return $this->redirectToRoute('userEdit');


        // ...
    }
}