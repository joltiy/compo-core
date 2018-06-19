<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;

/**
 * {@inheritdoc}
 */
class NameExtension extends AbstractAdminExtension
{
    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        if (!$this->isUseEntityTraits($listMapper->getAdmin(), [
            'Compo\Sonata\AdminBundle\Entity\NameEntityTrait',
        ])) {
            return;
        }

        return;
        if (!$listMapper->has('name')) {
            $listMapper->add('name');

            $keys = $listMapper->keys();

            usort($keys, function ($a, $b) {
                if (\in_array($a, ['id', 'batch', '_action'], true) || \in_array($b, ['id', 'batch', '_action'], true)) {
                    return 0;
                }

                if ('name' === $a) {
                    return -1;
                }

                if ('name' === $b) {
                    return 1;
                }

                return 0;
            });

            $listMapper->reorder($keys);
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if (!$this->isUseEntityTraits($datagridMapper->getAdmin(), [
            'Compo\Sonata\AdminBundle\Entity\NameEntityTrait',
        ])) {
            return;
        }

        if (!$datagridMapper->has('name')) {
            $datagridMapper->add('name', null, [
                'global_search' => true,
            ]);
        } else {
            $datagridMapper->get('name')->setOption('global_search', true);
        }
    }

    /**
     * @param AdminInterface $admin
     * @param                $object
     */
    public function prePersist(AdminInterface $admin, $object)
    {
        /** @var ModelManager $modelManager */
        $modelManager = $admin->getModelManager();

        $modelManager->getEntityManager($object)->getFilters()->disable('softdeleteable');
    }

    /**
     * @param AdminInterface $admin
     * @param                $object
     */
    public function postPersist(AdminInterface $admin, $object)
    {
        /** @var ModelManager $modelManager */
        $modelManager = $admin->getModelManager();

        $modelManager->getEntityManager($object)->getFilters()->enable('softdeleteable');
    }
}
