<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Site;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends EasyAdminController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{alias}_{id}", name="site")
     */
    public function sites(Site $sites)
    {
        return $this->render('default/site.html.twig',  ['sites' => $sites]);
    }

}