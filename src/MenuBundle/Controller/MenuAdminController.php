<?php

namespace Compo\MenuBundle\Controller;

use Compo\Sonata\AdminBundle\Controller\CRUDController;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MenuAdminController extends CRUDController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function treeAction(Request $request = null)
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        /** @var NestedTreeRepository $repo */
        $repo = $em->getRepository($this->admin->getClass());
        $tree = $repo->childrenHierarchy();


        return $this->render($this->admin->getTemplate('tree'), array(
            'action' => 'tree',
            'nodes' => $tree,
            'form' => $this->admin->getDatagrid()->getForm()->createView(),
        ));
    }


    /**
     * Contextualize the admin class depends on the current request
     *
     * @throws \RuntimeException
     */
    protected function configure()
    {
        parent::configure();
        $this->admin->setTemplate('tree', 'CompoMenuBundle:Admin:tree.html.twig');
    }

}
