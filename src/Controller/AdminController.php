<?php


namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends EasyAdminController
{
    /**
     * @return RedirectResponse
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

//        return $this->redirectToRoute('_dmi_backend', array(
//
//            'action' => 'edit',
//            'id' => $id,
//            'entity' => $this->request->query->get('entity'),
//        ));
    }

}