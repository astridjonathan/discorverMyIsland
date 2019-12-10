<?php


namespace App\Controller;


use App\Entity\Site;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends EasyAdminController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{alias}_{id}")
     */
    public function site(Site $site)
    {
        return $this->render('site/site.html.twig',  ['site' => $site]);
    }
}