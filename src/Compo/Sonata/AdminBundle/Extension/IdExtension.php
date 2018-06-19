<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class IdExtension extends AbstractAdminExtension
{
    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        if (!$this->isUseEntityTraits($listMapper->getAdmin(), [
            'Compo\Sonata\AdminBundle\Entity\IdEntityTrait',
        ])) {
            return;
        }

        if (!$listMapper->has('id')) {
            $listMapper->add('id');
        }

        $listMapper->get('id')->setTemplate('SonataIntlBundle:CRUD:list_integer.html.twig');

        return;
        $keys = $listMapper->keys();

        usort($keys, function ($a, $b) {
            if (\in_array($a, ['batch', '_action'], true) || \in_array($b, ['batch', '_action'], true)) {
                return 0;
            }

            if ('id' === $a) {
                return -1;
            }

            if ('id' === $b) {
                return 1;
            }

            return 0;
        });

        $listMapper->reorder($keys);
    }

    /**
     * @param FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        if (!$this->isUseEntityTraits($formMapper->getAdmin(), [
            'Compo\Sonata\AdminBundle\Entity\IdEntityTrait',
        ])) {
            return;
        }

        /** @var \Compo\Sonata\AdminBundle\Admin\AbstractAdmin $admin */
        $admin = $formMapper->getAdmin();

        if ($admin->isCurrentRoute('create')) {
            if ($formMapper->has('id')) {
                $formMapper->remove('id');
            }
        } else {
            $this->replaceFormField(
                $formMapper,
                'id',
                'text',
                [
                    'required' => false,
                    'attr' => ['readonly' => true],
                    'disabled' => true,
                ]
            );
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if (!$this->isUseEntityTraits($datagridMapper->getAdmin(), [
            'Compo\Sonata\AdminBundle\Entity\IdEntityTrait',
        ])) {
            return;
        }

        if (!$datagridMapper->has('id')) {
            $datagridMapper->add('id', null, [
                'global_search' => true,
            ]);
        } else {
            $datagridMapper->get('id')->setOption('global_search', true);
        }
    }
}
