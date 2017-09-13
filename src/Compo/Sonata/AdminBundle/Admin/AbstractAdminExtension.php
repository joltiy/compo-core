<?php

namespace Compo\Sonata\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdminExtension as BaseAbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritDoc}
 */
class AbstractAdminExtension extends BaseAbstractAdminExtension
{
    /**
     * @param FormMapper $formMapper
     * @param $name
     * @param null $type
     * @param array $options
     * @param array $fieldDescriptionOptions
     */
    public function replaceFormField(FormMapper $formMapper, $name, $type = null, array $options = array(), array $fieldDescriptionOptions = array())
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
                if (in_array($group, $tb_item['groups'], true)) {
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