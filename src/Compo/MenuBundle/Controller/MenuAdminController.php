<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\MenuBundle\Controller;

use Compo\Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * {@inheritdoc}
 */
class MenuAdminController extends CRUDController
{
    public function menuTargetItemsAction()
    {
        $result = [];

        $request = $this->getRequest();

        $type = $request->get('type', false);

        if ('url' !== $type) {
            $menuManager = $this->get('compo_menu.manager');

            $menuType = $menuManager->getMenuType($type);

            $choices = $menuType->getChoices();

            foreach ($choices as $name => $id) {
                $result[] = [
                    'id' => $id,
                    'text' => $name,
                ];
            }
        }

        return new JsonResponse($result);
    }
}
