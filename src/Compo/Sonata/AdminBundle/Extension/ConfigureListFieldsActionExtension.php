<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * {@inheritdoc}
 */
class ConfigureListFieldsActionExtension extends AbstractAdminExtension
{
    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        if (!$listMapper->has('_action')) {
            $listMapper->add('_action');
        }

        /** @var AbstractAdmin $admin */
        $admin = $listMapper->getAdmin();

        $_action = $listMapper->get('_action');

        if (null !== $_action) {
            $options = $_action->getOptions();

            if (
                !isset($options['actions']['delete'])
                &&
                $admin->hasRoute('delete')
                &&
                $admin->hasAccess('delete')
            ) {
                $options['actions']['delete'] = [
                    'template' => 'SonataAdminBundle:CRUD:list__action_delete.html.twig',
                ];
            }

            $_action->setOptions($options);
        }
    }
}
