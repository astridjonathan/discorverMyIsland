<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class AdminController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function restockAction()
    {
        $id = $this->request->query->get('id');
        $entity = $this->em->getRepository(Product::class)->find($id);
        $entity->setStock(100 + $entity->getStock());
        $this->em->flush();

        return $this->redirectToRoute('_discovermyisland_backend', array(
            'action' => 'list',
            'entity' => $this->request->query->get('entity'),
        ));

        return $this->redirectToRoute('_discovermyisland_backend', array(
            'action' => 'edit',
            'id' => $id,
            'entity' => $this->request->query->get('entity'),
        ));
    }

}