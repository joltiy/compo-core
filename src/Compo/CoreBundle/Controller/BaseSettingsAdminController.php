<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Controller;

use Sylius\Bundle\SettingsBundle\Controller\SettingsController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SettingsAdminController.
 */
class BaseSettingsAdminController extends SettingsController
{
    /**
     * @var string
     */
    public $translationDomain = 'messages';
    /**
     * @var string
     */
    protected $namespace = 'compo_core_settings';

    /**
     * @param Request $request
     * @param string  $namespace
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $namespace = null)
    {
        if (null === $namespace) {
            $namespace = $this->getNamespace();
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

        $admin = $admin_pool->getAdminByAdminCode('compo_core.admin.settings');

        $admin->setRequest($request);

        return $this->render(
            'CompoCoreBundle:Admin:settings.html.twig',
            [
                'action' => 'list',
                'breadcrumbs_builder' => $this->get('sonata.admin.breadcrumbs_builder'),
                'base_template' => 'CompoSonataAdminBundle::standard_layout_compo.html.twig',
                'admin' => $admin,
                'settings' => $settings,
                'form' => $form->createView(),
                'admin_pool' => $admin_pool,
                'translation_domain' => $this->getTranslationDomain(),
            ]
        );
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getTranslationDomain()
    {
        return $this->translationDomain;
    }

    /**
     * @param string $translationDomain
     */
    public function setTranslationDomain($translationDomain)
    {
        $this->translationDomain = $translationDomain;
    }
}
