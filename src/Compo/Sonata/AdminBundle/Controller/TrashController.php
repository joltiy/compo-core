<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Controller;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Filter\SoftDeleteableTrashFilter;
use Doctrine\ORM\EntityManager;
use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Корзина.
 */
class TrashController extends CRUDController
{
    /**
     * Корзина.
     *
     * @return Response
     */
    public function trashAction()
    {
        /** @var AbstractAdmin $admin */
        $admin = $this->getAdmin();

        $admin->checkAccess('undelete');

        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getManager();

        $filters = $em->getFilters();

        if ($filters->isEnabled('softdeleteable')) {
            $filters->disable('softdeleteable');
        }

        if (!$filters->isEnabled('softdeleteabletrash')) {
            $filters->enable('softdeleteabletrash');
        }

        /** @var SoftDeleteableTrashFilter $softdeleteabletrash */
        $softdeleteabletrash = $filters->getFilter('softdeleteabletrash');

        $softdeleteabletrash->enableForEntity($admin->getClass());

        $datagrid = $admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension(FormExtension::class)->renderer->setTheme($formView, $admin->getFilterTheme());

        return $this->renderWithExtraParams('@CompoSonataAdmin/CRUD/trash.html.twig', [
            'action' => 'trash',
            'form' => $formView,
            'datagrid' => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ]);
    }

    /**
     * Восстановление из корзины.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function untrashAction(Request $request)
    {
        /** @var AbstractAdmin $admin */
        $admin = $this->getAdmin();

        $admin->checkAccess('undelete');

        /** @var \Sonata\DoctrineORMAdminBundle\Model\ModelManager $modelManager */
        $modelManager = $admin->getModelManager();

        /** @var EntityManager $em */
        $em = $modelManager->getEntityManager($admin->getClass());

        $filters = $em->getFilters();

        if ($admin->getParent()) {
            $filters->enable('softdeleteable');
            $filters->enable('softdeleteabletrash');

            /** @var SoftDeleteableFilter $softdeleteable */
            $softdeleteable = $filters->getFilter('softdeleteable');

            /** @var SoftDeleteableTrashFilter $softdeleteabletrash */
            $softdeleteabletrash = $filters->getFilter('softdeleteabletrash');

            $parentEntityClass = $admin->getParent()->getClass();

            $softdeleteable->enableForEntity($parentEntityClass);
            $softdeleteabletrash->disableForEntity($parentEntityClass);

            $softdeleteable->disableForEntity($admin->getClass());
        } else {
            if ($filters->isEnabled('softdeleteable')) {
                $filters->disable('softdeleteable');
                $filters->enable('softdeleteabletrash');
            }
        }

        $id = $request->get($admin->getIdParameter());
        $object = $admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if ('POST' === $request->getMethod()) {
            // check the csrf token
            $this->validateCsrfToken('sonata.untrash');

            $translator = $this->get('translator');

            try {
                $object->setDeletedAt(null);
                $admin->update($object);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'ok']);
                }

                $this->addFlash('sonata_flash_info', $translator->trans('flash_untrash_successfull', []));
            } catch (\Exception $e) {
                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'error']);
                }

                $this->addFlash('sonata_flash_info', $translator->trans('flash_untrash_error', []));
            }

            return new RedirectResponse($admin->generateUrl('list'));
        }

        return $this->renderWithExtraParams('@CompoSonataAdmin/CRUD/untrash.html.twig', [
            'object' => $object,
            'action' => 'untrash',
            'csrf_token' => $this->getCsrfToken('sonata.untrash'),
        ]);
    }
}
