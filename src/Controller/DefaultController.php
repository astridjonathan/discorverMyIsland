<?php


namespace App\Controller;


use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Course;
use App\Entity\Site;
use App\Entity\Visit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tightenco\Collect\Support\Collection;

class DefaultController extends AbstractController
{
    /**
     * @return Response
     *
     */
    public function index()
    {

        # Get course
        $course = $this->getDoctrine()
            ->getRepository(Course::class)
            ->find(1);

        # Get associated visits
        $visits = new Collection($course->getVisits());
        $visits = $visits->sortBy(function ($visit) {
            return $visit->getPriority();
        });

        /** @var Visit $visit */
        foreach ($visits as $visit) {
            # Get site name for each visit by priority
            dump($visit->getSite()->getName());
            dump($visit->getPriority());
        }

        die;

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
     * @Route("/{category}/{alias}_{id}", name="default_site", methods={"GET"})
     */
    public function site(Site $site)
    {
        return $this->render('default/site.html.twig', ['site' => $site]);
    }


}