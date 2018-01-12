<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * {@inheritdoc}
 */
class ActionsExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;

    public function configureListFields(ListMapper $listMapper)
    {
        if ($listMapper->has('_action')) {
            $_action = $listMapper->get('_action');

            if (null !== $_action) {
                $options = $_action->getOptions();

                if (!isset($options['actions'])) {
                    if (isset($listMapper->getAdmin()->treeEnabled) && $listMapper->getAdmin()->treeEnabled) {
                        $options['actions']['create_parent'] = [
                            'template' => 'SonataAdminBundle:CRUD:list__action_create_parent.html.twig',
                        ];
                    }
                    /*
                    $options['actions']['edit'] = array(
                        'template' => 'SonataAdminBundle:CRUD:list__action_edit.html.twig'
                    );
                    */

                    $options['actions']['delete'] = [
                        'template' => 'SonataAdminBundle:CRUD:list__action_delete.html.twig',
                    ];

                    $options['actions']['clone'] = [
                        'template' => 'SonataAdminBundle:CRUD:list__action_clone.html.twig',
                    ];

                    /*
                    $options['actions']['history'] = array(
                        'template' => 'SonataAdminBundle:CRUD:list__action_history.html.twig'
                    );
                    */

                    if (method_exists($listMapper->getAdmin(), 'generatePermalink')) {
                        $options['actions']['show_on_site'] = [
                            'template' => 'SonataAdminBundle:CRUD:list__action_show_on_site.html.twig',
                        ];
                    }
                }

                $_action->setOptions($options);
            }
        }
    }
}
