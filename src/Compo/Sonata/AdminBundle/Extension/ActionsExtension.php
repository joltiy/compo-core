<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * {@inheritDoc}
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
                    /*
                    $options['actions']['edit'] = array(
                        'template' => 'SonataAdminBundle:CRUD:list__action_edit.html.twig'
                    );
                    */

                    $options['actions']['delete'] = array(
                        'template' => 'SonataAdminBundle:CRUD:list__action_delete.html.twig'
                    );

                    $options['actions']['clone'] = array(
                        'template' => 'SonataAdminBundle:CRUD:list__action_clone.html.twig'
                    );

                    /*
                    $options['actions']['history'] = array(
                        'template' => 'SonataAdminBundle:CRUD:list__action_history.html.twig'
                    );
                    */

                    if (method_exists($listMapper->getAdmin(), 'generatePermalink')) {
                        $options['actions']['show_on_site'] = array(
                            'template' => 'SonataAdminBundle:CRUD:list__action_show_on_site.html.twig'
                        );
                    }
                }

                $_action->setOptions($options);
            }
        }


    }
}