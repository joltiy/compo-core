<?php

namespace Compo\MenuBundle\Controller;

use Compo\Sonata\AdminBundle\Controller\CRUDController;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * {@inheritDoc}
 */
class MenuItemAdminController extends CRUDController
{
    public function getTreeNodes($request) {
        $em = $this->getDoctrine()->getManager();


        /** @var NestedTreeRepository $repo */
        $repo = $em->getRepository($this->admin->getClass());
        $repo->verify();
        $repo->recover();
        $em->flush();

        $node = $repo->findOneBy(array('menu' => $request->get('id')));
        $tree = $repo->childrenHierarchy($node);

        return $tree;
    }
}
