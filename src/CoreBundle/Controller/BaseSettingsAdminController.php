<?php

namespace Compo\CoreBundle\Controller;

use Sylius\Bundle\SettingsBundle\Controller\SettingsController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SettingsAdminController
 *
 * @package Compo\CoreBundle\Controller
 */
class BaseSettingsAdminController extends SettingsController
{
    public $translationDomain = 'messages';
    protected $namespase = 'compo_core_settings';

    /**
     * @param Request $request
     * @param string $namespace
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $namespace = null)
    {

        if (is_null($namespace)) {
            $namespace = $this->getNamespase();
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

                $message = $this->getTranslator()->trans('settings.updated_successful', array());
                $this->get('session')->getFlashBag()->add('sonata_flash_success', $message);

                return $this->redirect($request->headers->get('referer'));
            }
        }

        $admin_pool = $this->get('sonata.admin.pool');

        $admin = $admin_pool->getAdminByAdminCode('compo_core.admin.settings');

        $admin->setRequest($request);

        return $this->render('CompoCoreBundle:Admin:settings.html.twig', array(
            'action' => 'list',
            'breadcrumbs_builder' => $this->get('sonata.admin.breadcrumbs_builder'),

            'admin' => $admin,
            'settings' => $settings,
            'form' => $form->createView(),
            'admin_pool' => $admin_pool,
            'translation_domain' => $this->getTranslationDomain()
        ));
    }

    /**
     * @return string
     */
    public function getNamespase()
    {
        return $this->namespase;
    }

    /**
     * @param string $namespase
     */
    public function setNamespase($namespase)
    {
        $this->namespase = $namespase;
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