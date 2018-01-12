<?php

namespace Compo\SonataImportBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class DefaultRepository extends EntityRepository
{
    public function pagerfanta(Request $request)
    {
        $sql = $this->createQueryBuilder('data');
        $sql->select('data');
        $sql->andWhere('data.uploadFile = :uploadFile');
        $sql->setParameter('uploadFile', $request->get('import_id'));

        switch ($request->get('type', 'all')) {
            case 'success':
                $sql->andWhere('data.status = 1 or data.status = 2 or data.status = 0');
                break;
            case 'nochanges':
                $sql->andWhere('data.status = 0');
                break;
            case 'new':
                $sql->andWhere('data.status = 1');
                break;
            case 'update':
                $sql->andWhere('data.status = 2');
                break;
            case 'error':
                $sql->andWhere('data.status = 3');
                break;
        }

        return $sql->getQuery();
    }

    public function count(array $where = [])
    {
        $sql = $this->createQueryBuilder('data');
        $sql->select('COUNT(data)');
        if (count($where)) {
            foreach ($where as $key => $value) {
                $sql->andWhere('data.' . $key . ' = :' . $key);
                $sql->setParameter($key, $value);
            }
        }

        return $sql->getQuery()->getSingleScalarResult();
    }
}
