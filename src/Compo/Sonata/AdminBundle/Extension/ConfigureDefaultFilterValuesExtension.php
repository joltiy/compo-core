<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * {@inheritdoc}
 */
class ConfigureDefaultFilterValuesExtension extends AbstractAdminExtension
{
    /**
     * @param AdminInterface $admin
     * @param array          $filterValues
     */
    public function configureDefaultFilterValues(AdminInterface $admin, array &$filterValues)
    {
        /* @var AbstractAdmin $admin */

        $admin->setPerPageOptions([50, 100, 500, 1000, 10000]);
        $admin->setMaxPerPage(50);
        $admin->setMaxPageLinks(50);

        $datagridValues = [
            '_page' => 1,
            '_per_page' => 50,
            '_sort_order' => 'DESC',
            '_sort_by' => 'id',
        ];

        $filterValues = array_merge(
            $filterValues, $datagridValues
        );

        if (!method_exists($admin, 'getUser')) {
            return;
        }

        $user = $admin->getUser();

        if ($user) {
            $userSettings = $user->getSettings();

            $key = $admin->getCode() . '._per_page';

            if (isset($userSettings[$key])) {
                $filterValues['_per_page'] = $userSettings[$key];
            } else {
                $request = $admin->getRequest();

                if ($request) {
                    $filter = $request->get('filter');

                    if (isset($filter['_per_page'])) {
                        $filterValues['_per_page'] = $userSettings[$key] = $filter['_per_page'];
                        $user->setSettings($userSettings);
                        $em = $admin->getContainer()->get('doctrine')->getManager();

                        $em->persist($user);
                        $em->flush();
                    }
                }
            }
        }
    }
}
