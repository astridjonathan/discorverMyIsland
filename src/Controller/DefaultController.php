<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Contact;
use App\Entity\Site;
use App\Entity\User;
use App\Entity\Course;
use App\Entity\Visit;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tightenco\Collect\Support\Collection;

class DefaultController extends AbstractController
{
    /**
     * @return Response
     * @Route("/", name="/",methods={"GET|POST"})
     *
     */
    public function index()
    {
        #Get Sites
        $sites = $this->getDoctrine()
            ->getRepository(Site::class)
            ->findAll();
        #Get Categories
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        ##Get Courses
        $courses = $this->getDoctrine()
            ->getRepository(Course::class)
            ->findAll();

        return $this->render('default/index.html.twig', [
            'sites' => $sites,
            'categories' => $categories,
            'courses' => $courses
        ]);

    }

    /****************************************************************************************************************/
    /**
     * @param Request $request
     * @return Response
     * @Route("/explore.html", name="default_explore", methods={"GET|POST"})
     */
    public function explore(Request $request)
    {

        # Get alias param from query
        $aliasCategory = $request->query->get('q');


        # Get doctrine repositories
        $siteRepository = $this->getDoctrine()
            ->getRepository(Site::class);
        $categoryRepository = $this->getDoctrine()
            ->getRepository(Category::class);

        # get sites from category
        $sites = $aliasCategory ? $categoryRepository->findOneByAlias($aliasCategory)->getSites() : $siteRepository->findAll();

        #Récupération des catégories
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('default/explore.html.twig', [
            'sites' => $sites,
            'categories' => $categories
        ]);
    }

    /****************************************************************************************************************/
    /**
     * @param Category $category
     * @return Response
     * @Route("/category/{alias}", name="default_category", methods={"GET"})
     */
    public function category($alias)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['alias' => $alias]);
        $sites = $category->getSites();
        return $this->render('default/category.html.twig',
            ['sites' => $category->getSites(),
                'category' => $category]);
    }

    /****************************************************************************************************************/
    /**
     * @param Site $site
     * @return Response
     * @Route("/{category}/{alias}_{id}.html", name="default_site", methods={"GET|POST"})
     */
    use HelperTrait;


    public function addComment(Site $site, Request $request)
    {
        #Ajout d'un commentaire
        $comment = new Comment();
        #Récupérer un user
        $user = $this->getUser();
        $comment->setSite($site);
        #On affecte le User au commentaire
        $comment->setUser($user);
        #Création d'un formulaire
        $form = $this->createFormBuilder($comment)
            #Titre de l'article
            ->add('title', TextType::class, [
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
            ->add('image', FileType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'dropify',
                    'placeholder' => 'Télécharger une image '
                ]
            ])
            #Bouton envoyer
            ->add('submit', SubmitType::class, [
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
            #return $this->redirectToRoute('default/single-site.html.twig');
        }
        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy(['site' => $site]);
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['id' => $user]);

        $courses=$this->getDoctrine()
           ->getRepository(Course::class)
           ->findAll();

        #Transmission du formulaire à la vue
        return $this->render('default/single-site.html.twig', [
            'site' => $site,
            'user' => $user,
            'comments' => $comments,
            'courses'=> $courses,
            'form' => $form->createView()

        ]);
    }

    /****************************************************************************************************************/
    public function menu()
    {
        #Récupération des categories
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        #transmission à la vue
        return $this->render('components/_nav.html.twig', [
            'categories' => $categories
        ]);
    }

    /****************************************************************************************************************/
    /**
     * @return Response
     * @Route("/concept.html", name="default_concept", methods={"GET"})
     */
    public function concept()
    {
        return $this->render('default/concept.html.twig');
    }
    /****************************************************************************************************************/
    /**
     * @return Response
     * @Route("/contact.html", name="default_contact", methods={"GET|POST"})
     */

        public function contact(Request $request)
    {

        $contact = new Contact();

        $form = $this->createFormBuilder($contact)
            ->add('Firstname', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prénom'
                ]
            ])
            ->add('Lastname', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('Email', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Email'
                ]

            ])
            ->add('Subject', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Sujet du message'
                ]
            ])
            ->add('Message', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Entrez votre message'
                ]
            ])

            # Submit Button
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn btn-block dorne-btn'
                ]
            ])
            # crée le formulaire
            ->getForm();


        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            #Notification flash
            $this->addFlash('notice', 'Félicitations votre message a bien été envoyé !');

            #Redirection
            return $this->redirectToRoute('default_contact');
        }

            return $this->render('default/contact.html.twig', [
                'form' => $form->createView()
            ]);

    }
    /****************************************************************************************************************/
    /**
     * @return Response
     * @Route("/mentions-legales.html", name="default_mlegales", methods={"GET"})
     */
    public function mLegales()
    {
        return $this->render('default/mentions-legales.html.twig');
    }

    /****************************************************************************************************************/
    /**
     * @return Response
     * @Route("/parcours/{alias}.html", name="default_course", methods={"GET"})
     */

    public function course($alias)
    {
        # Get course
        $course = $this->getDoctrine()
            ->getRepository(Course::class)
            ->findOneBy(['alias' => $alias]);

        # Get associated visits
        $visits = new Collection($course->getVisits());
        $visits = $visits->sortBy(function ($visit) {
            return $visit->getPriority();
        });


        return $this->render('default/course.html.twig',
            [
                'alias'=>$alias,
            ]);
    }
}