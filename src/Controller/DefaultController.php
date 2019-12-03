<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @return Response
     * @Route("/")
     */
    public function index()
    {
        return $this->render('default/index.html.twig');
        #return new Response('<h1>Accueil</h1>');
    }


}