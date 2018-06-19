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
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;

/**
 * {@inheritdoc}
 */
class ConfigureListFieldsPersistentExtension extends AbstractAdminExtension
{
    /**
     * Сохранение отображаемых столбцов.
     *
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        /** @var AbstractAdmin $admin */
        $admin = $listMapper->getAdmin();

        if (!method_exists($admin, 'getUser')) {
            return;
        }

        $user = $admin->getUser();

        if ($user) {
            $userSettings = $user->getSettings();

            $key = $admin->getCode() . '.list.fields';

            if (isset($userSettings[$key])) {
                $fields = $userSettings[$admin->getCode() . '.list.fields'];
            } else {
                $fields = [];
            }
        } else {
            $fields = [];
        }

        if (\count($fields)) {
            $keys = $listMapper->keys();

            foreach ($keys as $key) {
                $_action = $listMapper->get($key);

                $_action_options = $_action->getOptions();

                $_action_options['active'] = true;

                if (\in_array($key, ['batch', 'id', 'name', '_action'], true)) {
                    $_action_options['active'] = true;
                } elseif (!\in_array($key, $fields, true)) {
                    $_action_options['active'] = false;
                } else {
                    $_action_options['active'] = true;
                }

                $_action->setOptions($_action_options);
            }
        } else {
            $elements = $admin->getList()->getElements();

            /**
             * @var string
             * @var FieldDescription $item
             */
            foreach ($elements as $key => $item) {
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

            if ($user) {
                $userSettings[$admin->getCode() . '.list.fields'] = $fields;

                $user->setSettings($userSettings);

                $em = $this->getContainer()->get('doctrine')->getManager();

                $em->persist($user);
                $em->flush();
            }
        }
    }
}
