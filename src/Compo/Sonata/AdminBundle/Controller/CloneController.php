<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Клонирование элемента.
 */
class CloneController extends CRUDController
{
    /**
     * @return RedirectResponse
     */
    public function cloneAction()
    {
        $admin = $this->getAdmin();

        $object = $admin->getSubject();

        $admin->checkAccess('edit');
        $admin->checkAccess('create');

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', 0));
        }

        $clonedObject = clone $object;

        if (method_exists($clonedObject, 'setName')) {
            $clonedObject->setName($object->getName() . ' (Copy)');
        }

        if (method_exists($clonedObject, 'setSlug')) {
            $clonedObject->setSlug('clone-slug-' . $clonedObject->getSlug());
        }

        if (method_exists($clonedObject, 'setCreatedAt')) {
            $clonedObject->setCreatedAt(new \DateTime());
        }

        if (method_exists($clonedObject, 'setCreatedBy')) {
            $clonedObject->setCreatedBy($this->getUser());
        }

        if (method_exists($clonedObject, 'setUpdatedAt')) {
            $clonedObject->setUpdatedAt(new \DateTime());
        }

        if (method_exists($clonedObject, 'setUpdatedBy')) {
            $clonedObject->setUpdatedBy($this->getUser());
        }

        $clonedObject->setId(null);

        $admin->create($clonedObject);

        $this->addFlash('sonata_flash_info', $this->get('translator')->trans('sonata_flash_success_clone', []));

        return new RedirectResponse($admin->generateUrl('list', ['filter' => $admin->getFilterParameters()]));
    }
}
