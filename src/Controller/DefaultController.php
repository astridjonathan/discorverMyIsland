<?php


namespace App\Controller;


use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @return Response
     *
     */
    public function index()
    {

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
        #Récupération de tous les sites
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
     * @Route("/{category}/{alias}_{id}.html", name="default_site", methods={"GET"})
     */
    public function site(Site $site)
    {
        return $this->render('default/single-site.html.twig', ['site' => $site]);
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




}