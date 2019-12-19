<?php


namespace App\Controller;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends EasyAdminController
{
    /**
     * @return RedirectResponse
     *
     */
    public function restockAction()
    {


        return $this->redirectToRoute('adminDMI', array(

            'action' => 'list',
            'entity' => $this->request->query->get('entity'),
        ));

//
    }

}