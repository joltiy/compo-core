<?php

declare(strict_types=1);

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SecurityRolesType.
 */
class SecurityRolesType extends \Sonata\UserBundle\Form\Type\SecurityRolesType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['roles_groups'] = $options['roles_groups'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        /** @var \Compo\Sonata\UserBundle\Security\EditableRolesBuilder $rolesBuilder */
        $rolesBuilder = $this->rolesBuilder;

        $groups = $rolesBuilder->getRolesGroups();

        $resolver->setDefault('roles_groups', $groups);

        $resolver->setDefault('choice_label', function ($value, $key, $index) use ($groups) {
            if (isset($groups['labels'][$key])) {
                return $groups['labels'][$key];
            }

            return $key;
        });
    }
}
