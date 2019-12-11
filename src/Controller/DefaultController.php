<?php


namespace App\Controller;



use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Site;
use App\Entity\User;
use App\Entity\Course;
use App\Entity\Visit;

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
     * @Route("/", name="/",methods={"GET"})
     *
     */
    public function index()
    {

        /*# Get course
        $course = $this->getDoctrine()
            ->getRepository(Course::class)
            ->find(1);

        # Get associated visits
        $visits = new Collection($course->getVisits());
        $visits = $visits->sortBy(function ($visit) {
            return $visit->getPriority();
        });*/

        #/** @var Visit $visit */
        #foreach ($visits as $visit) {
        #    # Get site name for each visit by priority
        #    dump($visit->getSite()->getName());
        #    dump($visit->getPriority());
        #}

        #die;
        $sites = $this->getDoctrine()
            ->getRepository(Site::class)
            ->findAll();
        $categories= $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        return $this->render('default/index.html.twig', [
            'sites' => $sites,
            'categories' => $categories
        ]);

    }

    /**
     * @param $alias
     * @return Response
     * @Route("/explore.html", name="default_explore", methods={"GET"})
     */
    public function explore()
    {
        #Récupération de tous les sites et des catégories
        $sites = $this->getDoctrine()
            ->getRepository(Site::class)
            ->findAll();
        $categories= $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();


        return $this->render('default/explore.html.twig', [
            'sites' => $sites,
            'categories' => $categories,

        ]);

    }
    /**
     * @param Category $category
     * @return Response
     * @Route("/category/{alias}", name="default_category", methods={"GET"})
     */
    public function category($alias)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['alias'=>$alias]);
        $sites = $category->getSites();
        return $this->render('default/category.html.twig',
            ['sites'=>$category->getSites(),
                'category'=>$category]);
    }

    /**
     * @param Site $site
     * @return Response
     * @Route("/{category}/{alias}_{id}.html", name="default_site", methods={"GET|POST"})
     */

    use HelperTrait;
    public function addComment(Site $site,  Request $request)
    {

        #Ajout d'un commentaire
        $comment= new Comment();
        #Récupérer un user
        $user = $this->getUser();
        $comment->setSite($site);

        #On affecte le User au commentaire

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
            #return $this->redirectToRoute('default/single-site.html.twig');

        }

        $comments= $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy(['site'=>$site]);
        $user=$this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['id'=>$user]);


        #Transmission du formulaire à la vue
        return $this->render('default/single-site.html.twig',[
            'site'=> $site,
            'user'=>$user,
            'comments'=> $comments,
            'form' => $form->createView()
        ]);

    }


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

    /**
 * @return Response
 * @Route("/concept.html", name="default_concept", methods={"GET"})
 */
    public function concept()
    {
        return $this->render('default/concept.html.twig');
    }

    /**
     * @return Response
     * @Route("/contact.html", name="default_contact", methods={"GET"})
     */
    public function contact()
    {
        return $this->render('default/contact.html.twig');
    }

    /**
     * @return Response
     * @Route("/mentions-legales.html", name="default_mlegales", methods={"GET"})
     */
    public function mLegales()
    {
        return $this->render('default/mentions-legales.html.twig');
    }


    /**
     * @return Response
     * @Route("/map.html", name="map", methods={"GET"})
     */
    public function map()
    {
        return $this->render('default/map.html.twig');
    }
}