<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Сортировка по позиции, смена родителя.
 */
class PositionController extends CRUDController
{
    /**
     * Сортировка по позиции.
     *
     * @return Response
     */
    public function sortableAction()
    {
        $admin = $this->getAdmin();

        $admin->checkAccess('edit');

        $request = $this->getRequest();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository($admin->getClass());

        $object = $repository->find($request->request->get('id'));

        // Получаем позицию элемента, после которого должен стоять текущий

        $after_object = $repository->find($request->request->get('after_id'));

        $after_pos = 0;

        if ($after_object) {
            $after_pos = $after_object->getPosition();
        }

        $new_pos = 0;

        // Если позиция не определена, то 1, иначе + 1 от позиции после которого должен стоять.
        if ($after_object) {
            $new_pos = $after_pos + 1;
        }

        // Обновляем позиции текущего
        $object->setPosition($new_pos);

        $admin->update($object);

        return $this->renderJson(
            [
                'result' => 'ok',
                'objectId' => $admin->getNormalizedIdentifier($object),
            ]
        );
    }
}
