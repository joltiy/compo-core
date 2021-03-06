<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Filter;

use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\Filter;

/**
 * Фильтрация по числовому диапозону.
 */
class NumberRangeFilter extends Filter
{
    /**
     * @var bool
     */
    protected $range = true;

    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $data)
    {
        /* @var QueryBuilder $queryBuilder */
        // check data sanity

        if (!$data || !\is_array($data) || !\array_key_exists('value', $data)) {
            return;
        }

        if ($this->range && \is_array($data['value'])) {
            // additional data check for ranged items
            //if (!array_key_exists('start', $data['value']) || !array_key_exists('end', $data['value'])) {
            //$data['value']['start'] = 0;
            //return;
            //}

            //if (!$data['value']['start'] || !$data['value']['end']) {
            //return;
            //}

            if (\array_key_exists('start', $data['value']) && $data['value']['start']) {
                $startQuantity = $this->getNewParameterName($queryBuilder);
                $this->applyWhere($queryBuilder, sprintf('%s.%s %s :%s', $alias, $field, '>=', $startQuantity));
                $queryBuilder->setParameter($startQuantity, $data['value']['start']);
            }

            if (\array_key_exists('end', $data['value']) && $data['value']['end']) {
                $endQuantity = $this->getNewParameterName($queryBuilder);
                $this->applyWhere($queryBuilder, sprintf('%s.%s %s :%s', $alias, $field, '<=', $endQuantity));
                $queryBuilder->setParameter($endQuantity, $data['value']['end']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderSettings()
    {
        return [
            'sonata_type_filter_default',
            [
                'field_type' => $this->getFieldType(),
                'field_options' => $this->getFieldOptions(),
                'operator_type' => 'hidden',
                'operator_options' => [],
                'label' => $this->getLabel(),
            ],
        ];
    }
}
