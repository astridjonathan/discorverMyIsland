<?php


namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/inscription.html",name="user_register", methods={"GET|POST"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        #1. Création d'un nouvel utilisateur
        $user = new User();
        $user->setRoles(['ROLE_USER']);

        #2. Création du formulaire
        $form = $this->createFormBuilder($user)
            #Pseudo
            ->add('pseudo',TextType::class,[
                'label' => false,
                'attr' => [
                    'placeholder' => 'Votre pseudonyme'
                ]
            ])
            #Email
            ->add('email',EmailType::class,[
                'label' => false,
                'attr' => [
                    'placeholder' => 'Votre email'
                ]
            ])
            #Password
            ->add('password',PasswordType::class,[
                'label' => false,
                'attr' => [
                    'placeholder' => 'Mot de passe'
                ]
            ])

            #Bouton envoyer
            ->add('submit', SubmitType::class,[
                'label' => 'S\'inscrire',
                'attr' => [
                    'class'=> 'btn btn-block dorne-btn'
                ]
            ])
            #Creates Form
            ->getForm();
        #3. Vérification de la soumission
        $form->handleRequest($request);

        #5. Sauvegarde en BDD
        if ($form->isSubmitted() && $form->isValid()) {
            #4. Encodage du MDP(ignorer cette étape)
            $user->setPassword(
                $encoder->encodePassword($user, $user->getPassword())
            );

            #Sauvegarde dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            #6. Notification flash
            $this->addFlash('notice', 'Félicitations votre inscription est prise en compte !');

            #7. Redirection sur la page de connexion
            return $this->redirectToRoute('default_index',[
                'id'=>$user->getId()
            ]);
        }
        #Transmission du formulaire à la vue
        return $this->render('user/register.html.twig',[
            'form' => $form->createView()
        ]);
    }
}