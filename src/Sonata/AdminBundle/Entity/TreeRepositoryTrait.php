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
}