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
class ListFieldsExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;


    public function configureListFields(ListMapper $listMapper)
    {


        $user = $listMapper->getAdmin()->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();


        $userSettings = $user->getSettings();

        if (isset($userSettings[$listMapper->getAdmin()->getCode().'.list.fields'])) {
            $fields = $userSettings[$listMapper->getAdmin()->getCode().'.list.fields'];
        } else {
            $fields = array();
        }


        if (count($fields)) {
            $keys = $listMapper->keys();

            foreach ($keys as $key) {
                $_action = $listMapper->get($key);
                $_action_options = $_action->getOptions();

                $_action_options['active'] = true;

                if (in_array($key, array('batch', 'id', 'name', '_action'))) {
                    $_action_options['active'] = true;
                } elseif (!in_array($key, $fields)) {
                    $_action_options['active'] = false;
                } else {
                    $_action_options['active'] = true;
                }

                $_action->setOptions($_action_options);


            }

        } else {
            $elements = $listMapper->getAdmin()->getList()->getElements();

            foreach ($elements as $item) {
                $options = $item->getOptions();

                $options['active'] = true;

                if (!isset($options['default']) || !$options['default']) {
                    $fields[] = $item->getName();
                    $options['active'] = true;
                }

                if (!isset($options['default'])) {
                    $options['default'] = true;
                    $options['active'] = true;
                } else {
                    $options['active'] = $options['default'];
                }


                $item->setOptions($options);
            }

            $userSettings[$listMapper->getAdmin()->getCode().'.list.fields'] = $fields;

            $user->setSettings($userSettings);

            $this->getContainer()->get('doctrine')->getManager()->persist($user);
            $this->getContainer()->get('doctrine')->getManager()->flush();
        }
    }
}