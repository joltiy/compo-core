<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * {@inheritdoc}
 */
class UpdateAssociationExtension extends AbstractAdminExtension
{
    /**
     * {@inheritdoc}
     */
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        $collection->add('update_many_to_many', $admin->getRouterIdParameter() . '/update_many_to_many', ['_controller' => 'CompoSonataAdminBundle:UpdateAssociation:updateManyToMany']);
        $collection->add('update_many_to_one', $admin->getRouterIdParameter() . '/update_many_to_one', ['_controller' => 'CompoSonataAdminBundle:UpdateAssociation:updateManyToOne']);

        return $collection;
    }
}
