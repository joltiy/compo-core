<?php

namespace Compo\Sonata\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdminExtension as BaseAbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class AbstractAdminExtension extends BaseAbstractAdminExtension
{
    /**
     * @param \Sonata\AdminBundle\Admin\AdminInterface $admin
     * @param array                                    $traits
     *
     * @return bool
     */
    public function isUseEntityTraits($admin, array $traits = [])
    {
        $traitsAdmin = class_uses($admin->getClass());

        foreach ($traits as $trait) {
            if (
                !in_array($trait, $traitsAdmin)
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param FormMapper $formMapper
     * @param $name
     * @param null  $type
     * @param array $options
     * @param array $fieldDescriptionOptions
     */
    public function replaceFormField(FormMapper $formMapper, $name, $type = null, array $options = [], array $fieldDescriptionOptions = [])
    {
        $admin = $formMapper->getAdmin();

        $fg = $admin->getFormGroups();
        $tb = $admin->getFormTabs();

        $keys = $formMapper->keys();

        if ($formMapper->has($name)) {
            $group = '';
            $tab = '';

            foreach ($fg as $fg_key => $fg_item) {
                if (isset($fg_item['fields'][$name])) {
                    $group = $fg_key;
                }
            }

            foreach ($tb as $tb_key => $tb_item) {
                if (in_array($group, $tb_item['groups'])) {
                    $tab = $tb_key;
                }
            }

            $admin->removeFormFieldDescription($name);
            $admin->removeFieldFromFormGroup($name);
            $formMapper->getFormBuilder()->remove($name);
            $formMapper->getFormBuilder()->getForm()->remove($name);

            $formMapper->remove($name);

            if ($formMapper->hasOpenTab()) {
                $formMapper->end();
            }
            if ($formMapper->hasOpenTab()) {
                $formMapper->end();
            }
            $formMapper->tab($tab);

            $formMapper->with($group);

            $formMapper->add($name, $type, $options, $fieldDescriptionOptions);

            $formMapper->end();
            $formMapper->end();

            $formMapper->reorder($keys);

            $admin->setFormTabs($tb);
            $admin->setFormGroups($fg);
        }
    }
}
