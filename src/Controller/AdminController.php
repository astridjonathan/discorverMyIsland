<?php


namespace App\Controller;
namespace EasyCorp\Bundle\EasyAdminBundle;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use EasyCorp\Bundle\EasyAdminBundle;

class AdminController extends EasyAdminController
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function restockAction()
    {
        #$id = $this->request->query->get('id');
        #$entity = $this->em->getRepository(Product::class)->find($id);
        #$entity->setStock(100 + $entity->getStock()); mis pour une gestio nde stock, site de commerce
        #$this->em->flush();


        return $this->redirectToRoute('_dmi_backend', array(

            'action' => 'list',
            'entity' => $this->request->query->get('entity'),
        ));

        return $this->redirectToRoute('_dmi_backend', array(

            'action' => 'edit',
            'id' => $id,
            'entity' => $this->request->query->get('entity'),
        ));
    }

}