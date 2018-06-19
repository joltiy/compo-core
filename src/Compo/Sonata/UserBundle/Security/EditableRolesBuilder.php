<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\UserBundle\Security;

/**
 * Class EditableRolesBuilder.
 */
class EditableRolesBuilder extends \Sonata\UserBundle\Security\EditableRolesBuilder
{
    /**
     * @return array
     */
    public function getRolesGroups()
    {
        $pool = $this->pool;
        $tokenStorage = $this->tokenStorage;
        $authorizationChecker = $this->authorizationChecker;
        $rolesHierarchyList = $this->rolesHierarchy;

        $container = $pool->getContainer();

        $translator = $container->get('translator');

        $groups = [];
        $labels = [];

        $roles = [];
        $rolesReadOnly = [];

        if (!$tokenStorage->getToken()) {
            return $groups;
        }

        $isMaster = $authorizationChecker->isGranted('ROLE_SUPER_ADMIN');

        // get roles from the service container
        foreach ($rolesHierarchyList as $name => $rolesHierarchy) {
            if ($isMaster || $authorizationChecker->isGranted($name)) {
                $roles[$name] = $name;

                if (!isset($groups['ROLE_MAIN_GROUP'])) {
                    $groups['ROLE_MAIN_GROUP'] = [];
                }

                $groups['ROLE_MAIN_GROUP'][] = $name;

                $labels[$name] = $name;

                /** @var array $rolesHierarchy */
                foreach ($rolesHierarchy as $role) {
                    if (!isset($roles[$role]) && !\in_array($role, [
                            'ROLE_SONATA_ADMIN',
                            'ROLE_USER',
                            'ROLE_SONATA_MEDIA_ADMIN_MEDIA_LIST',
                            'ROLE_SONATA_MEDIA_ADMIN_MEDIA_CREATE',
                            'ROLE_SONATA_MEDIA_ADMIN_MEDIA_VIEW',
                            'ROLE_SONATA_MEDIA_ADMIN_MEDIA_EDIT',
                        ], true)) {
                        $roles[$role] = $role;

                        if (!isset($groups['ROLE_MAIN_GROUP'])) {
                            $groups['ROLE_MAIN_GROUP'] = [];
                        }

                        $groups['ROLE_MAIN_GROUP'][] = $role;

                        $labels[$role] = $role;
                    }
                }
            }
        }

        // get roles from the Admin classes
        foreach ($pool->getAdminServiceIds() as $id) {
            try {
                $admin = $pool->getInstance($id);
            } catch (\Exception $e) {
                continue;
            }

            $isMaster = $admin->isGranted('MASTER');
            $securityHandler = $admin->getSecurityHandler();

            $baseRole = $securityHandler->getBaseRole($admin);

            if ('' === $baseRole) { // the security handler related to the admin does not provide a valid string
                continue;
            }

            $items = [
                'LIST',
                'VIEW',
                'EDIT',
                'CREATE',
                'DELETE',
                'UNDELETE',
                'EXPORT',
                'IMPORT',
                'SETTINGS',
            ];

            if (\in_array($id, [
                'sonata.media.admin.media',
                'sonata.media.admin.gallery',
                'sonata.media.admin.gallery_has_media',
                'sonata.page.admin.snapshot',
                'sonata.page.admin.shared_block',
            ], true)) {
                continue;
            }

            foreach ($items as $role_item) {
                $role = sprintf($baseRole, $role_item);
                $role_group = sprintf($baseRole, 'GROUP');

                //$role_label = $translator->trans($admin->getLabel(), [], $admin->getTranslationDomain())
                //    . ' - '
                //    . $translator->trans('ROLE_' . $role_item, [], $admin->getTranslationDomain());

                $role_label = $role;

                if ($isMaster) {
                    if (!isset($groups[$role_group])) {
                        $groups[$role_group] = [];
                    }

                    $groups[$role_group][] = $role;
                    $labels[$role] = $translator->trans('ROLE_' . $role_item, [], $admin->getTranslationDomain());

                    // if the user has the MASTER permission, allow to grant access the admin roles to other users
                    $roles[$role] = $role_label;
                } elseif ($authorizationChecker->isGranted($role)) {
                    // although the user has no MASTER permission, allow the currently logged in user to view the role
                    $rolesReadOnly[$role] = $role_label;
                }
            }
        }

        return [
            'groups' => $groups,
            'labels' => $labels,
        ];
    }

    /**
     * @param bool $domain
     * @param bool $expanded
     *
     * @return array
     */
    public function getRoles($domain = false, $expanded = true)
    {
        $pool = $this->pool;
        $tokenStorage = $this->tokenStorage;
        $authorizationChecker = $this->authorizationChecker;
        $rolesHierarchyList = $this->rolesHierarchy;

        $roles = [];
        $rolesReadOnly = [];

        if (!$tokenStorage->getToken()) {
            return [$roles, $rolesReadOnly];
        }

        $isMaster = $authorizationChecker->isGranted('ROLE_SUPER_ADMIN');

        // get roles from the service container
        foreach ($rolesHierarchyList as $name => $rolesHierarchy) {
            if ($isMaster || $authorizationChecker->isGranted($name)) {
                $roles[$name] = $name;

                /** @var array $rolesHierarchy */
                foreach ($rolesHierarchy as $role) {
                    if (!isset($roles[$role]) && !\in_array($role, [
                            'ROLE_SONATA_ADMIN',
                            'ROLE_USER',
                            'ROLE_SONATA_MEDIA_ADMIN_MEDIA_LIST',
                            'ROLE_SONATA_MEDIA_ADMIN_MEDIA_CREATE',
                            'ROLE_SONATA_MEDIA_ADMIN_MEDIA_VIEW',
                            'ROLE_SONATA_MEDIA_ADMIN_MEDIA_EDIT',
                        ], true)) {
                        $roles[$role] = $role;
                    }
                }
            }
        }

        // get roles from the Admin classes
        foreach ($pool->getAdminServiceIds() as $id) {
            try {
                $admin = $pool->getInstance($id);
            } catch (\Exception $e) {
                continue;
            }

            $isMaster = $admin->isGranted('MASTER');
            $securityHandler = $admin->getSecurityHandler();

            $baseRole = $securityHandler->getBaseRole($admin);

            if ('' === $baseRole) { // the security handler related to the admin does not provide a valid string
                continue;
            }

            $items = [
                'LIST',
                'VIEW',
                'EDIT',
                'CREATE',
                'DELETE',
                'UNDELETE',
                'EXPORT',
                'IMPORT',
                'SETTINGS',
            ];

            if (\in_array($id, [
                'sonata.media.admin.media',
                'sonata.media.admin.gallery',
                'sonata.media.admin.gallery_has_media',
                'sonata.page.admin.snapshot',
                'sonata.page.admin.shared_block',
            ], true)) {
                continue;
            }

            foreach ($items as $role_item) {
                $role = sprintf($baseRole, $role_item);

                //$role_label = $translator->trans($admin->getLabel(), [], $admin->getTranslationDomain())
                //    . ' - '
                //    . $translator->trans('ROLE_' . $role_item, [], $admin->getTranslationDomain());

                $role_label = $role;

                if ($isMaster) {
                    // if the user has the MASTER permission, allow to grant access the admin roles to other users
                    $roles[$role] = $role_label;
                } elseif ($authorizationChecker->isGranted($role)) {
                    // although the user has no MASTER permission, allow the currently logged in user to view the role
                    $rolesReadOnly[$role] = $role_label;
                }
            }
        }

        return $roles;
    }
}
