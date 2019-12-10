<?php


namespace App\Controller;

use App\Entity\Course;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CourseController extends EasyAdminController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addCourse(Request $request)
    {
        # ajouter un parcours / formulaire
        $course = new Course();
        $form = $this->createFormBuilder($course)
            ->add('name', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom du parcours'
                ]
            ])

            ->add('duration', TextType::class, [
                'required' => true,
                'label' => false
            ])

            ->add('content', TextareaType::class, [
                'required' => true,
                'label' => false
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Nouveau parcours'
            ])

            ->getForm();

        $form->handleRequest($request);

        return $this->render('course/course.html.twig',  ['course' => $course]);
    }
}