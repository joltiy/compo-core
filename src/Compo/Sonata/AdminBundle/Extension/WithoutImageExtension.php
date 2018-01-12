<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

/**
 * {@inheritdoc}
 */
class WithoutImageExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if ($datagridMapper->has('without_image')) {
            $datagridMapper->remove('without_image');

            $datagridMapper->add(
                'without_image',
                'doctrine_orm_callback',
                [
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!$value['value']) {
                            return false;
                        }

                        /* @var QueryBuilder $queryBuilder */
                        $queryBuilder->andWhere($queryBuilder->getRootAliases()[0] . '.image IS NULL');

                        return true;
                    },
                    'field_type' => 'checkbox',
                ]
            );
        }
    }
}
