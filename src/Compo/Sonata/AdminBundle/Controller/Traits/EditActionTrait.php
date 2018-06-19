<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Controller\Traits;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Разрешаем просмотр удалённых элементов.
 */
trait EditActionTrait
{
    /**
     * Edit action.
     *
     * @param int|string|null $id
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     *
     * @return Response|RedirectResponse
     */
    public function editAction($id = null)
    {
        if ($this->getRequest()->query->get('trash')) {
            /** @var Container $container */
            $container = $this->getContainer();

            /** @var EntityManager $em */
            $em = $container->get('doctrine')->getManager();

            $filters = $em->getFilters();

            // Разрешаем просмотр удалённых элементов
            if ($filters->isEnabled('softdeleteable')) {
                $filters->disable('softdeleteable');
            }
        }

        return parent::editAction($id);
    }
}
