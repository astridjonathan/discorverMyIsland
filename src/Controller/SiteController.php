<?php


namespace App\Controller;


use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
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