<?php

namespace Compo\MenuBundle\Controller;

use Compo\Sonata\AdminBundle\Controller\CRUDController;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * {@inheritdoc}
 */
class MenuItemAdminController extends CRUDController
{
    public function getTreeNodes($request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var NestedTreeRepository $repo */
        $repo = $em->getRepository($this->admin->getClass());
        $repo->verify();
        $repo->recover();
        $em->flush();

        $node = $repo->findOneBy(array('menu' => $request->get('id')));
        $tree = $repo->childrenHierarchyWithNodes($node);

        return $tree;
    }
}
