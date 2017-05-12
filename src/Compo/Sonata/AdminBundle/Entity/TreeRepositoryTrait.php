<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\QueryBuilder;

trait TreeRepositoryTrait
{
    public function findAll()
    {
        return $this->findBy(array(), array('lft' => 'ASC'));
    }


    public function getForTreeSelector($exclude_id = false, $qb_callback = null)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->select('c')
            ->orderBy('c.lft', 'ASC');

        if ($exclude_id) {
            $queryBuilder->where('c.id <> :id')
                ->setParameter('id', $exclude_id);
        }

        if ($qb_callback) {
            call_user_func_array($qb_callback, array($queryBuilder));
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function getFlatNodes($startNode = null, $options = null)
    {
        if (is_null($options)) {
            $options = array(
                'decorate' => false,
                'rootOpen' => '',
                'rootClose' => '',
                'childOpen' => '',
                'childClose' => '',
                'nodeDecorator' => function ($node) {
                    return '' . $node['name'] . '';
                }
            );
        }
        $htmlTree = $this->childrenHierarchy(
            $startNode, // starting from root nodes
            false, // load all children, not only direct
            $options
        );
        return $this->toFlat($htmlTree, ' » ');
    }

    public function toFlat($node, $sep = ' > ', $path = '')
    {
        $els = array();
        foreach ($node as $id => $opts) {
            $els[$opts['id']] = $path . $opts['name'];
            if (isset($opts['__children']) && is_array($opts['__children']) && sizeof($opts['__children'])) {
                $r = $this->toFlat($opts['__children'], $sep, ($path . $opts['name'] . $sep));
                foreach ($r as $r_id => $title) {
                    $els[$r_id] = $title;
                }
            }
        }
        return $els;
    }


    public function childrenHierarchyWithNodes($node = null, $direct = false, array $options = array(), $includeNode = false) {

        $tree = $this->childrenHierarchy($node, $direct, $options, $includeNode);

        return $this->fillTreeNodes($tree);
    }





    /**
     * Заполнить дерево объектов сущностями
     *
     * @param $tree
     *
     * @return mixed
     */
    public function fillTreeNodes($tree)
    {
        $ids = $this->getIdsForTree($tree);

        $nodes_array = $this->findBy(array('id' => $ids));

        $nodes = array();

        foreach ($nodes_array as $nodes_array_item) {


            $nodes[$nodes_array_item->getId()] = $nodes_array_item;
        }

        $tree = $this->fillTreeNodesForItems($tree, $nodes);

        return $tree;
    }

    /**
     * Вернуть ids каталогов, для дерева
     *
     * @param $tree
     *
     * @return array
     */
    protected function getIdsForTree($tree)
    {
        $ids = array();

        foreach ($tree as $key => $item) {
            $ids[] = $item['id'];

            $ids = array_merge($ids, $this->getIdsForTree($tree[$key]['__children']));
        }

        return $ids;
    }

    /**
     * Заполнить дерево объектов сущностями
     *
     * @param $tree
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