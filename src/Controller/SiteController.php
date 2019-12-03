<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteController extends AbstractController
{

    public function site()
    {
        return $this->render('site/site.html.twig');
    }
}