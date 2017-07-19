<?php
/**
 * Created by IntelliJ IDEA.
 * User: Quentin
 * Date: 30/06/2017
 * Time: 20:20
 */

namespace AppBundle\Controller;

use AppBundle\DataAccess\CommentData;
use AppBundle\DataAccess\NotationData;
use AppBundle\DataAccess\ProjectData;
use AppBundle\DataAccess\ResearchData;
use AppBundle\DataAccess\TechData;
use AppBundle\DataAccess\GroupeData;
use AppBundle\DataAccess\UserData;
use AppBundle\Entity\CommentForm;
use AppBundle\Entity\Groupe;
use AppBundle\Entity\ProjectForm;
use AppBundle\Entity\ResearchForm;
use AppBundle\Entity\Techno;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ResearchController extends Controller
{
    /**
     * @Route("/research", name="research")
     */
    public function researchUser(Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if (!($id = UserData::isLogged($em, $session)))
            return $this->redirectToRoute('log');

        $sign = new ResearchForm();
        $form = $this->createFormBuilder($sign)
            ->add('choices', ChoiceType::class, array(
                'choices' => TechData::getAll($em),
                'multiple' => true,
                'expanded' => true,
                'choice_label' => function ($category, $key, $index) {
                    /** @var Techno $category */
                    return strtoupper($category->getName());
                },
                'choice_attr' => function ($category, $key, $index) {
                    return [strval($category->getIdtechno())];
                },
            ))
            ->add('groupes', ChoiceType::class, array(
                'choices' => GroupeData::getAll($em),
                'multiple' => true,
                'expanded' => true,
                'choice_label' => function ($category, $key, $index) {
                    /** @var Groupe $category */
                    return strtoupper($category->getName());
                },
                'choice_attr' => function ($category, $key, $index) {
                    return [strval($category->getIdgroupe())];
                },
            ))
            ->add('save', SubmitType::class, array('label' => 'Valider'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sign = $form->getData();


            $val = ResearchData::research($em, $sign);
            for ($i = 0; $i < count($val); $i++) {
                $val[$i]->noteTech = NotationData::getAVGTech($em, $val[$i]->getIduser());
                $val[$i]->noteHuman = NotationData::getNotationForHuman($em, $val[$i]->getIduser());
            }
            return $this->render('default/search.html.twig', array(
                'form' => $form->createView(),
                'err' => '',
                'arr' => $val,
            ));
        }

        return $this->render('default/search.html.twig', array(
            'form' => $form->createView(),
            'err' => '',
            'arr' => array()
        ));
    }

}
