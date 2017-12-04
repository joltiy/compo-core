<?php

namespace Compo\CoreBundle\Controller;

use Compo\Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SettingsAdminController.
 */
class AboutProjectAdminController extends CRUDController
{
    public function listAction(Request $request = null)
    {
        $admin_pool = $this->get('sonata.admin.pool');

        $admin = $this->getAdmin();

        return $this->render(
            'CompoCoreBundle:Admin:about_project.html.twig',
            array(
                'action' => 'list',
                'breadcrumbs_builder' => $this->get('sonata.admin.breadcrumbs_builder'),
                'base_template' => 'CompoSonataAdminBundle::standard_layout_compo.html.twig',
                'admin' => $admin,
                'admin_pool' => $admin_pool,
                'translation_domain' => $this->getAdmin()->getTranslationDomain(),
            )
        );
    }
}
