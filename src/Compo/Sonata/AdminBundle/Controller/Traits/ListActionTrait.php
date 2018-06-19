<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Controller\Traits;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * ListActionTrait.
 */
trait ListActionTrait
{
    /**
     * Редирект, в случае фильтрации ManyToMany.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    protected function preList(Request $request)
    {
        /** @var AbstractAdmin $admin */
        $admin = $this->getAdmin();

        $filters = $request->query->get('filter', []);

        $parentAssociationMapping = $admin->getParentAssociationMapping();

        if ($parentAssociationMapping && $admin->isChild()) {
            $name = str_replace('.', '__', $parentAssociationMapping);

            if (isset($filters[$name]) && \is_array($filters[$name]['value'])) {
                $targetAdmin = $admin->getConfigurationPool()->getAdminByAdminCode($admin->getCode());

                if (\count($filters[$name]['value']) > 1) {
                    return new RedirectResponse(
                        $targetAdmin->generateUrl('list', ['filter' => $filters])
                    );
                }

                if ($filters[$name]['value'][0] !== $request->get($admin->getParent()->getIdParameter())) {
                    return new RedirectResponse(
                        $targetAdmin->generateUrl('list', ['filter' => $filters])
                    );
                }
            }
        }

        return null;
    }
}
