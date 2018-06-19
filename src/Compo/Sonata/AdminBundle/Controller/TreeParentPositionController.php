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
class TreeParentPositionController extends CRUDController
{
    /**
     * Перемещение элемента в другой родительский элемент
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

        if ($admin->isChild()) {
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
                $currentNode->setParent($targetNode->getParent());

                if ($targetNode->getPosition()) {
                    $currentNode->setPosition($targetNode->getPosition() - 1);
                } else {
                    $currentNode->setPosition(0);
                }
                break;
            case 'after':
                $currentNode->setParent($targetNode->getParent());
                $currentNode->setPosition($targetNode->getPosition() + 1);
                break;
            case 'append':
                $currentNode->setParent($targetNode);
                $currentNode->setPosition(-1);
                break;
        }

        $admin->update($currentNode);
        $admin->update($targetNode);

        return $this->renderJson(['result' => true]);
    }
}
