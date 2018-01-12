<?php

namespace Compo\SonataImportBundle\Admin;

use Sonata\AdminBundle\Route\RouteCollection;

trait AdminImportTrait
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('import', 'import', [
            '_controller' => 'CompoSonataImportBundle:Default:index',
        ]);
        $collection->add('upload', '{import_id}/upload', [
            '_controller' => 'CompoSonataImportBundle:Default:upload',
        ]);
        $collection->add('importStatus', '{import_id}/upload/status', [
            '_controller' => 'CompoSonataImportBundle:Default:importStatus',
        ]);
    }

    public function getDashboardActions()
    {
        $actions = parent::getDashboardActions();

        $actions['import'] = [
            'label' => 'Import',
            'url' => $this->generateUrl('import'),
            'icon' => 'upload',
            'translation_domain' => 'SonataAdminBundle', // optional
            'template' => 'SonataAdminBundle:CRUD:dashboard__action.html.twig', // optional
        ];

        return $actions;
    }
}
