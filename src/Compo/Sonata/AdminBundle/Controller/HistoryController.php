<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Controller;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * HistoryController.
 */
class HistoryController extends CRUDController
{
    /**
     * Восстановление ревизии.
     *
     * @param $revision
     *
     * @return RedirectResponse|Response
     */
    public function historyRevertAction($revision)
    {
        /** @var AbstractAdmin $admin */
        $admin = $this->getAdmin();

        $admin->checkAccess('edit');

        $request = $this->getRequest();

        $id = $request->get($admin->getIdParameter());

        $object = $admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if ('POST' === $request->getMethod()) {
            // check the csrf token
            $this->validateCsrfToken('sonata.history.revert');

            $entityClass = $admin->getClass();

            $translator = $this->get('translator');

            try {
                $manager = $this->get('sonata.admin.audit.manager');

                if (!$manager->hasReader($entityClass)) {
                    throw new NotFoundHttpException(sprintf('unable to find the audit reader for class : %s', $entityClass));
                }

                $reader = $manager->getReader($entityClass);

                $reader->revert($object, $revision);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'ok']);
                }

                $this->addFlash('sonata_flash_info', $translator->trans('flash_history_revert_successfull', []));
            } catch (\Exception $e) {
                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'error']);
                }

                $this->addFlash('sonata_flash_info', $translator->trans('flash_history_revert_error', []));
            }

            return new RedirectResponse($admin->generateUrl('list'));
        }

        return $this->renderWithExtraParams('@CompoSonataAdmin/CRUD/history_revert.html.twig', [
            'object' => $object,
            'revision' => $revision,
            'action' => 'revert',
            'csrf_token' => $this->getCsrfToken('sonata.history.revert'),
        ]);
    }
}
