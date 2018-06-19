<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Controller;

use Compo\Sonata\AdminBundle\Controller\Traits\BaseCRUDControllerTrait;
use Sonata\AdminBundle\Controller\CRUDController as BaseCRUDController;
use Sylius\Bundle\SettingsBundle\Form\Factory\SettingsFormFactoryInterface;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * {@inheritdoc}
 */
class CRUDController extends BaseCRUDController
{
    use BaseCRUDControllerTrait;

    /**
     * @param Request $request
     * @param string  $namespace
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function settingsAction(Request $request, $namespace = null)
    {
        $admin = $this->getAdmin();

        $admin->checkAccess('settings');

        if (null === $namespace) {
            $namespace = $admin->getSettingsNamespace();
        }

        $manager = $this->getSettingsManager();
        $settings = $manager->load($namespace);

        $form = $this
            ->getSettingsFormFactory()
            ->create($namespace);

        $form->setData($settings);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $manager->save($form->getData());

                $message = $this->getTranslator()->trans('settings.updated_successful');
                $this->get('session')->getFlashBag()->add('sonata_flash_success', $message);

                return $this->redirect($request->headers->get('referer'));
            }
        }

        $admin_pool = $this->get('sonata.admin.pool');

        return $this->renderWithExtraParams(
            'CompoCoreBundle:Admin:settings.html.twig',
            [
                'action' => 'settings',
                'breadcrumbs_builder' => $this->get('sonata.admin.breadcrumbs_builder'),
                'admin' => $admin,
                'base_template' => 'CompoSonataAdminBundle::standard_layout_compo.html.twig',

                'settings' => $settings,
                'form' => $form->createView(),
                'admin_pool' => $admin_pool,
                'translation_domain' => $admin->getTranslationDomain(),
            ]
        );
    }

    /**
     * @return SettingsManagerInterface
     */
    protected function getSettingsManager()
    {
        return $this->container->get('sylius.settings_manager');
    }

    /**
     * @return SettingsFormFactoryInterface
     */
    protected function getSettingsFormFactory()
    {
        return $this->container->get('sylius.form_factory.settings');
    }
}
