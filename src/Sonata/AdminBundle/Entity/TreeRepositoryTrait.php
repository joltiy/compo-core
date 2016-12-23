<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

trait TreeRepositoryTrait
{
    public function findAll()
    {
        return $this->findBy(array(), array('lft' => 'ASC'));
    }


    public function getForTreeSelector($exclude_id = false)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->select('c')
            ->orderBy('c.lft', 'ASC');

        if ($exclude_id) {
            $queryBuilder->where('c.id <> :id')
                ->setParameter('id', $exclude_id);
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