<?php


namespace App\Controller;


use App\Entity\User;
use http\Env\Response;
use EasyCorp\Bundle\EasyAdminBundle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    use HelperTrait;
    /**
     * @Route("/inscription.html",name="user_register", methods={"GET|POST"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        #Création d'un nouvel utilisateur
        $user = new User();
        $user->setRoles(['ROLE_USER']);

        # Création du formulaire
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
            #AVATAR upload
            ->add('avatar', FileType::class,[
                'required'=> false,
                'label' => false,
                'attr' => [
                    'class' => 'dropify',
                    'placeholder' => 'Télécharger un avatar '
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
        # Vérification de la soumission
        $form->handleRequest($request);


        #Sauvegarde en BDD
        if ($form->isSubmitted() && $form->isValid()) {
            # Encodage du MDP(ignorer cette étape)
            $user->setPassword(
                $encoder->encodePassword($user, $user->getPassword())
            );
            #upload de l'avatar
            /** @var UploadedFile $avatarFile */
            $avatarFile = $form['avatar']->getData();
            if ($avatarFile) {
                $newFilename = $this->slugify($user->getPseudo()) . '-' . uniqid() . '.' . $avatarFile->guessExtension();
                try {
                    $avatarFile->move(
                        $this->getParameter('avatar_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $user->setAvatar($newFilename);
            } #fin upload avatar

            #Génération nom avatar
            $user->setPseudo($this->slugify($user->getPseudo()));
            #Sauvegarde dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            # Notification flash
            $this->addFlash('notice', 'Félicitations votre inscription est prise en compte !');

            # Redirection sur la page de connexion

            return $this->redirectToRoute('app_login', [
                'id'=>$user->getId()
            ]);
        }
        #Transmission du formulaire à la vue
        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * @Route("/connexion.html", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): \Symfony\Component\HttpFoundation\Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('default_index');
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);

    }

    /**
     * @Route("/deconnexion.html", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}