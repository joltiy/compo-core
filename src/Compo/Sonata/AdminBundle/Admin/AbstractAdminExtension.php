<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Admin;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\Traits\IsUseEntityTraitsTrait;
use Compo\Sonata\UserBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdminExtension as BaseAbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class AbstractAdminExtension extends BaseAbstractAdminExtension
{
    use IsUseEntityTraitsTrait;
    use ContainerAwareTrait;

    /**
     * Конфигурация режимов отображения.
     *
     * @param AdminInterface $admin
     */
    public function configureListMode(AdminInterface $admin)
    {
    }

    /**
     * Конфигурация шаблонов.
     *
     * @param AdminInterface $admin
     */
    public function configureTemplates(AdminInterface $admin)
    {
    }

    /**
     * Замена существующего элемента формы.
     *
     * @param FormMapper $formMapper
     * @param string     $name
     * @param string     $type
     * @param array      $options
     * @param array      $fieldDescriptionOptions
     */
    public function replaceFormField(FormMapper $formMapper, $name, $type = null, array $options = [], array $fieldDescriptionOptions = [])
    {
        $admin = $formMapper->getAdmin();

        /** @var array $fg */
        $fg = $admin->getFormGroups();

        /** @var array $tb */
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
                if (\in_array($group, $tb_item['groups'])) {
                    $tab = $tb_key;
                }
            }

            $admin->removeFormFieldDescription($name);
            $admin->removeFieldFromFormGroup($name);

            $formBuilder = $formMapper->getFormBuilder();

            $formBuilder->remove($name);
            $formBuilder->getForm()->remove($name);

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

    /**
     * Get a user from the Security Token Storage.
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @return User|null
     *
     * @see TokenInterface::getUser()
     */
    public function getUser()
    {
        $container = $this->getContainer();

        if (!$container->has('security.token_storage')) {
            return null;
        }

        $tokenStorage = $container->get('security.token_storage');

        if (null === $token = $tokenStorage->getToken()) {
            return null;
        }

        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user;
    }
}
