<?php

namespace AppBundle\Controller;

use AppBundle\DataAccess\CommentData;
use AppBundle\DataAccess\NotationData;
use AppBundle\DataAccess\ProjectData;
use AppBundle\DataAccess\TechData;
use AppBundle\DataAccess\GroupeData;
use AppBundle\DataAccess\UserData;
use AppBundle\Entity\CommentForm;
use AppBundle\Entity\ProjectForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ProjectController extends Controller
{

    /**
     * @Route("/invite/{user}/{project}", name="inviteUser")
     */
    public function inviteUser($user, $project, EntityManagerInterface $em, SessionInterface $session)
    {
        //TODO check can invite DONE
        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');

        ProjectData::inviteUser($em, $user, $project, $id);
        return $this->redirectToRoute('displayUser', array('slug' => $user));
    }


    /**
     * @Route("/project/{project}", name="projectDetails")
     */
    public function projectDetail($project, EntityManagerInterface $em, SessionInterface $session)
    {

        //TODO: check user is in project DONE
        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');


        if (!ProjectData::isInProject($em, $project, $id)) {
            throw $this->createNotFoundException(
                'Not in project'
            );
        }
        return $this->render('default/projectDetail.html.twig', array(
            'project' => ProjectData::getProjectById($em, $project),
            'state' => ProjectData::getState($em, $project, $id),
        ));
    }

    /**
     * @Route("/project/join/{project}", name="projectJoin")
     */
    public function projectJoin($project, EntityManagerInterface $em, SessionInterface $session)
    {
        //TODO: check user is in project DONE

        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');
        if (!ProjectData::isInProject($em, $project, $id)) {
            ProjectData::join($em, $id, $project);
        }
        return $this->redirectToRoute('projectDetails', array('project' => $project));
    }


    /**
     * @Route("/project/leave/{project}", name="projectLeave")
     */
    public function leave($project, EntityManagerInterface $em, SessionInterface $session)
    {
        //TODO: check user is in project DONE

        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');
        if (ProjectData::isInProject($em, $project, $id)) {
            ProjectData::leaveProject($em, $id, $project);
        }
        return $this->redirectToRoute('main');
    }

    /**
     * @Route("/project/delete/{project}", name="projectDelete")
     */
    public function delete($project, EntityManagerInterface $em, SessionInterface $session)
    {
        //TODO: check user own project DONE

        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');
        if (ProjectData::ownProject($em, $project, $id))
            ProjectData::deleteProject($em, $project);
        return $this->redirectToRoute('main');
    }

    /**
     * @Route("/project/", name="project")
     */
    public function myProjet(EntityManagerInterface $em, SessionInterface $session)
    {
        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');

        return $this->render('default/project.html.twig', array(
            'invitation' => ProjectData::myInvitations($em, $id),
            'projects' => ProjectData::myProjects($em, $id),
            'owned' => ProjectData::owned($em, $id),
        ));
    }


    /**
     * @Route("/projectAdd/", name="addProject")
     */
    public function addProject(Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');

        $sign = new ProjectForm();
        $form = $this->createFormBuilder($sign)
            ->add('name', TextType::class, array('label' => 'Nom:'))
            ->add('description', TextType::class, array('label' => 'Description:'))
            ->add('begin', DateType::class, array('label' => 'Début:'))
            ->add('end', DateType::class, array('label' => 'Fin:'))
            ->add('save', SubmitType::class, array('label' => 'Valider'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sign = $form->getData();
            ProjectData::addProject($em, $id, $sign);
            return $this->render('default/projectAdd.html.twig', array(
                'form' => $form->createView(),
            ));

        }
        return $this->render('default/projectAdd.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}