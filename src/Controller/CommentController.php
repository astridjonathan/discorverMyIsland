<?php


namespace App\Controller;


use App\Entity\Comment;

class CommentController
{
    public function addComment()
    {
        #Création d'un nouvel article
        $comment= new Comment();
        #Récupérer un user en attendant user connecté
        $member = $this->getDoctrine()
            ->getRepository(User::class)
            ->find();
        #On affecte le User à l'article
        $comment->setUser($member);
        #Création d'un formulaire
        $form = $this->createFormBuilder($comment)
            #Titre de l'article
            ->add('title', TextType::class,[
                'required' =>true, #par defaut à true pas à mettre
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre de du commentaire'
                ]
            ])

            #Article's content
            ->add('content', TextareaType::class, [
                'required'=>false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Contenu de l\'article'
                ]
            ])
            #Image upload
            ->add('image', FileType::class,[
                'label' => false,
                'attr' => [
                    'class' => 'dropify',
                    'placeholder' => 'Télécharger une image '
                ]
            ])
            #Bouton envoyer
            ->add('submit', SubmitType::class,[
                'label' => 'Publier mon Article'
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
                $newFilename = $this->slugify($article->getTitle()) . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('articles_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $article->setImage($newFilename);
            } #fin upload image
            #Génération de l'alias de l'article
            $article->setAlias($this->slugify($article->getTitle()));
            #Sauvegarde dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            #Notification flash
            $this->addFlash('notice', 'Félicitations votre article est en ligne !');
            #Redirection
            return $this->redirectToRoute('default_article',[
                'category' => $article->getCategory()->getAlias(),
                'alias'=> $article->getAlias(),
                'id'=>$article->getId()
            ]);
        }
        #Transmission du formulaire à la vue
        return $this->render('default/single-site.html.twig',[
            'form' => $form->createView()
        ]);

    }
}