<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Site;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

use Symfony\Component\Routing\Annotation\Route;

class SiteController extends EasyAdminController
{

    use HelperTrait;

    /**
     * Formulaire permettant l'ajout d'un commentaire
     *
     *
     */
    public function addComment(Site $site, User $user, Request $request)
    {

        #Ajout d'un commentaire
        $comment= new Comment();
        #Récupérer un user
        $user = $this->getUser();
        #On affecte le User à l'article
        $comment->setUser($user);
        #Création d'un formulaire
        $form = $this->createFormBuilder($comment)
            #Titre de l'article
            ->add('title', TextType::class,[
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre de du commentaire'
                ]
            ])

            #Comment's content
            ->add('content', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Contenu du commentaire'
                ]
            ])
            #Image upload
            ->add('image', FileType::class,[
                'required'=>false,
                'label' => false,
                'attr' => [
                    'class' => 'dropify',
                    'placeholder' => 'Télécharger une image '
                ]
            ])
            #Bouton envoyer
            ->add('submit', SubmitType::class,[
                'label' => 'Publier un Commentaire',
                'attr' => [
                    'class' => 'btn btn-block dorne-btn',
                ]
            ])
            #Creates Form
            ->getForm();
        #Pemet à SF de gérer les données réçues
        $form->handleRequest($request);
        #Si le formulaire est soumis et que c'est validé
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form['image']->getData();
            if ($imageFile) {
                $newFilename = $this->slugify($comment->getTitle()) . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('comments_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $comment->setImage($newFilename);
            } #fin upload image
            #Génération de l'alias du commentaire
            $comment->setAlias($this->slugify($comment->getTitle()));
            #Sauvegarde dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            #Notification flash
            $this->addFlash('notice', 'Félicitations votre commentaire est en ligne !');
            #Redirection
            return $this->redirectToRoute('default/single-site.html.twig');

        }

        $comments= $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy(['site'=>$site]);
        $user=$this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['id'=>$user]);
        #Transmission du formulaire à la vue
        return $this->render('default/single-site.html.twig',[
            'form' => $form->createView(),
            'site'=> $site,
            'user'=>$user,
             'comments'=> $comments
        ]);

    }



}