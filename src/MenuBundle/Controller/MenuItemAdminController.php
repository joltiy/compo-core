<?php

namespace Compo\MenuBundle\Controller;

use Compo\Sonata\AdminBundle\Controller\CRUDController;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MenuItemAdminController extends CRUDController
{

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function treeAction(Request $request = null)
    {
        $request->getBasePath();

        if (isset($this->admin->treeEnabled) && $this->admin->treeEnabled) {

            if (false === $this->admin->isGranted('LIST')) {
                throw new AccessDeniedException();
            }
            $em = $this->getDoctrine()->getManager();

            /** @var NestedTreeRepository $repo */
            $repo = $em->getRepository($this->admin->getClass());

            $node = $repo->findOneBy(array('menu' => $request->get('id')));

            $tree = $repo->childrenHierarchy($node);

            return $this->render($this->admin->getTemplate('tree'), array(
                'action' => 'tree',
                'nodes' => $tree,
                'form' => $this->admin->getDatagrid()->getForm()->createView(),
            ));
        } else {
            return parent::listAction();
        }

    }

}
