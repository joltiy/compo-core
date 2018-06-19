<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Controller\Traits;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Пакетное действие включить/выключить.
 */
trait BatchActionEnabledTrait
{
    /**
     * @param ProxyQuery $selectedModelQuery
     * @param Request    $request
     *
     * @throws ModelManagerException
     *
     * @return RedirectResponse
     */
    public function batchActionDisable(ProxyQuery $selectedModelQuery, Request $request = null)
    {
        return $this->batchActionEnabled($selectedModelQuery, $request, false);
    }

    /**
     * @param ProxyQuery   $selectedModelQuery
     * @param Request|null $request
     * @param bool         $enabled
     *
     * @throws ModelManagerException
     *
     * @return RedirectResponse
     */
    public function batchActionEnabled(ProxyQuery $selectedModelQuery, Request $request = null, $enabled)
    {
        /** @var AbstractAdmin $admin */
        $admin = $this->getAdmin();

        $admin->checkAccess('edit');

        /** @var QueryBuilder $selectedModelQuery */

        /** @var ModelManager $modelManager */
        $modelManager = $admin->getModelManager();

        $aliases = $selectedModelQuery->getRootAliases();

        $selectedModelQuery->select('DISTINCT ' . $aliases[0]);

        try {
            $entityManager = $modelManager->getEntityManager($admin->getClass());

            $i = 0;

            foreach ($selectedModelQuery->getQuery()->iterate() as $pos => $object) {
                if (method_exists($object[0], 'setEnabled')) {
                    $object[0]->setEnabled($enabled);
                }

                $modelManager->update($object[0]);

                if (0 === (++$i % 100)) {
                    $entityManager->flush();
                    $entityManager->clear();
                }
            }

            $entityManager->flush();
            $entityManager->clear();
        } catch (\PDOException $e) {
            throw new ModelManagerException('', 0, $e);
        } catch (\Exception $e) {
            throw new ModelManagerException('', 0, $e);
        }

        if ($enabled) {
            $this->addFlash('sonata_flash_success', 'flash_batch.enable_success');
        } else {
            $this->addFlash('sonata_flash_success', 'flash_batch.disable_success');
        }

        return new RedirectResponse(
            $admin->generateUrl('list', ['filter' => $admin->getFilterParameters()])
        );
    }

    /**
     * @param ProxyQuery $selectedModelQuery
     * @param Request    $request
     *
     * @throws ModelManagerException
     *
     * @return RedirectResponse
     */
    public function batchActionEnable(ProxyQuery $selectedModelQuery, Request $request = null)
    {
        return $this->batchActionEnabled($selectedModelQuery, $request, true);
    }
}
