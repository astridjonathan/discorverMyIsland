<?php


namespace App\Controller;


use App\Entity\Site;
use EasyCorp\Bundle\EasyAdminBundle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends EasyAdminBundle
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