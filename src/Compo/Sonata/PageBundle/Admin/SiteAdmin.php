<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\PageBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\Traits\BaseAdminTrait;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Admin definition for the Site class.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SiteAdmin extends \Sonata\PageBundle\Admin\SiteAdmin
{
    use BaseAdminTrait;

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('enabled', null, ['editable' => true, 'label' => 'Включён'])
            ->add('host');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_site.label_general', ['class' => 'col-md-6'])
            ->add('name')
            //->add('isDefault', null, array('required' => false))
            ->add('enabled', null, ['required' => false])
            ->add('host')
            ->end()
            ->with('form_site.label_seo', ['class' => 'col-md-6'])
            ->add('title', null, ['required' => false])
            ->add('metaDescription', 'textarea', ['required' => false])
            ->add('metaKeywords', 'textarea', ['required' => false])
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        //$collection->remove('snapshots');
        $collection->remove('create');
        $collection->remove('remove');
        $collection->remove('delete');
        $collection->remove('show');

        //$collection->add('snapshots', $this->getRouterIdParameter().'/snapshots');
    }
}
