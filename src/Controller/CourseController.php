<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CourseController extends AbstractController
{
    public function course(Course $course)
    {
        $course = new Course();
        $course->
            ->
            ->
            ->
        return $this->render('course/course.html.twig',  ['course' => $course]);
    }

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
            ])
    }
}