<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Controller;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Клонирование элемента.
 */
class TreeNestedController extends CRUDController
{
    /**
     * Move element.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     *
     * @internal param string $position
     */
    public function moveAction(Request $request)
    {
        $admin = $this->getAdmin();

        $admin->checkAccess('edit');

        if ($this->admin->isChild()) {
            $id = $request->query->get('childId');
        } else {
            $id = $request->query->get('id');
        }

        $targetId = $request->query->get('target');
        $dropPosition = $request->query->get('position');

        $em = $this->getDoctrine()->getManager();

        /** @var NestedTreeRepository $repo */
        $repo = $em->getRepository($admin->getClass());

        $currentNode = $repo->find($id);
        $targetNode = $repo->find($targetId);

        switch ($dropPosition) {
            case 'before':
                $repo->persistAsPrevSiblingOf($currentNode, $targetNode);
                break;
            case 'after':
                $repo->persistAsNextSiblingOf($currentNode, $targetNode);
                /*
                                    if ($currentNode->getLft() < $targetNode->getLft() ) {
                                        $repo->persistAsNextSiblingOf($currentNode, $targetNode);

                                    } else {
                                        $repo->persistAsNextSiblingOf($targetNode, $currentNode);
                                    }
                */
                break;
            case 'append':
                $repo->persistAsLastChildOf($currentNode, $targetNode);
                break;
        }

        $admin->update($currentNode);
        $admin->update($targetNode);

        $response = new Response(json_encode(['result' => true]), 200);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
