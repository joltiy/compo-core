<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * Trait TreeRepositoryTrait.
 */
trait TreeTrait
{
    /**
     * @return mixed
     */
    public function findAll()
    {
        return $this->findBy([], ['lft' => 'ASC']);
    }

    /**
     * @param bool $exclude_id
     * @param null $qb_callback
     *
     * @return array
     */
    public function getForTreeSelector($exclude_id = false, $qb_callback = null)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder->select('c')->orderBy('c.lft', 'ASC');

        if ($exclude_id) {
            $queryBuilder->where('c.id <> :id')->setParameter('id', $exclude_id);
        }

        if ($qb_callback) {
            /* @noinspection VariableFunctionsUsageInspection */
            \call_user_func($qb_callback, $queryBuilder);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param null       $startNode
     * @param array|null $options
     *
     * @return array
     */
    public function getFlatNodes($startNode = null, $options = null)
    {
        if (null === $options) {
            $options = [
                'decorate' => false,
                'rootOpen' => '',
                'rootClose' => '',
                'childOpen' => '',
                'childClose' => '',
                'nodeDecorator' => function ($node) {
                    return '' . $node['name'] . '';
                },
            ];
        }
        $htmlTree = $this->childrenHierarchy(
            $startNode, // starting from root nodes
            false, // load all children, not only direct
            $options
        );

        return $this->toFlat($htmlTree, ' » ');
    }

    /**
     * @param array  $node
     * @param string $sep
     * @param string $path
     *
     * @return array
     */
    public function toFlat($node, $sep = ' > ', $path = '')
    {
        $els = [];

        foreach ($node as $id => $opts) {
            $els[$opts['id']] = $path . $opts['name'];
            if (isset($opts['__children']) && \is_array($opts['__children']) && \count($opts['__children'])) {
                $r = $this->toFlat($opts['__children'], $sep, $path . $opts['name'] . $sep);
                foreach ($r as $r_id => $title) {
                    $els[$r_id] = $title;
                }
            }
        }

        return $els;
    }

    /**
     * @param null  $node
     * @param bool  $direct
     * @param array $options
     * @param bool  $includeNode
     *
     * @return mixed
     */
    public function childrenHierarchyWithNodes($node = null, $direct = false, array $options = [], $includeNode = false)
    {
        $tree = $this->childrenHierarchy($node, $direct, $options, $includeNode);

        return $this->fillTreeNodes($tree);
    }

    /**
     * Заполнить дерево объектов сущностями.
     *
     * @param $tree
     *
     * @return mixed
     */
    public function fillTreeNodes($tree)
    {
        $ids = $this->getIdsForTree($tree);

        /** @var array $nodes_array */
        $nodes_array = $this->findBy(['id' => $ids]);

        $nodes = [];

        foreach ($nodes_array as $nodes_array_item) {
            /* @var $nodes_array_item \Compo\Sonata\AdminBundle\Entity\IdEntityTrait */
            $nodes[$nodes_array_item->getId()] = $nodes_array_item;
        }

        $tree = $this->fillTreeNodesForItems($tree, $nodes);

        return $tree;
    }

    /**
     * Вернуть ids каталогов, для дерева.
     *
     * @param array $tree
     *
     * @return array
     */
    protected function getIdsForTree($tree)
    {
        $ids = [];

        foreach ($tree as $key => $item) {
            $ids[] = $item['id'];

            /** @noinspection SlowArrayOperationsInLoopInspection */
            $ids = array_merge($ids, $this->getIdsForTree($tree[$key]['__children']));
        }

        return $ids;
    }

    /**
     * Заполнить дерево объектов сущностями.
     *
     * @param array $tree
     * @param $nodes
     *
     * @return mixed
     */
    protected function fillTreeNodesForItems($tree, $nodes)
    {
        foreach ($tree as $key => $item) {
            if (isset($nodes[$item['id']])) {
                $tree[$key]['node'] = $nodes[$item['id']];
            } else {
                $tree[$key]['node'] = null;
            }

            $tree[$key]['__children'] = $this->fillTreeNodesForItems($tree[$key]['__children'], $nodes);
        }

        return $tree;
    }
}
