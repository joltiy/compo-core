<?php

namespace Compo\Sonata\UserBundle\Security;

/**
 * Class EditableRolesBuilder.
 */
class EditableRolesBuilder extends \Sonata\UserBundle\Security\EditableRolesBuilder
{
    /**
     * @return array
     */
    public function getRoles($domain = false, $expanded = true)
    {
        $roles = [];
        $rolesReadOnly = [];

        if (!$this->tokenStorage->getToken()) {
            return [$roles, $rolesReadOnly];
        }

        $translator = $this->pool->getContainer()->get('translator');

        $isMaster = $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN');

        // get roles from the service container
        foreach ($this->rolesHierarchy as $name => $rolesHierarchy) {
            if ($isMaster || $this->authorizationChecker->isGranted($name)) {
                $roles[$name] = $translator->trans($name, [], 'messages');

                foreach ($rolesHierarchy as $role) {
                    if (!isset($roles[$role])) {
                        if (!in_array($role, [
                            'ROLE_SONATA_ADMIN',
                            'ROLE_USER',
                            'ROLE_SONATA_MEDIA_ADMIN_MEDIA_LIST',
                            'ROLE_SONATA_MEDIA_ADMIN_MEDIA_CREATE',
                            'ROLE_SONATA_MEDIA_ADMIN_MEDIA_VIEW',
                            'ROLE_SONATA_MEDIA_ADMIN_MEDIA_EDIT',
                        ])) {
                            $roles[$role] = $translator->trans($role, [], 'messages');
                        }
                    }
                }
            }
        }

        // get roles from the Admin classes
        foreach ($this->pool->getAdminServiceIds() as $id) {
            try {
                $admin = $this->pool->getInstance($id);
            } catch (\Exception $e) {
                continue;
            }

            $isMaster = $admin->isGranted('MASTER');
            $securityHandler = $admin->getSecurityHandler();
            // TODO get the base role from the admin or security handler
            $baseRole = $securityHandler->getBaseRole($admin);

            if ('' === $baseRole) { // the security handler related to the admin does not provide a valid string
                continue;
            }

            //dump($admin); exit;

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

            if (in_array($id, [
                'sonata.media.admin.media',
                'sonata.media.admin.gallery',
                'sonata.media.admin.gallery_has_media',
                'sonata.page.admin.snapshot',
                'sonata.page.admin.shared_block',
            ])) {
                continue;
            }

            foreach ($items as $role_item) {
                $role = sprintf($baseRole, $role_item);

                //dump($role);

                $role_label = $translator->trans($admin->getLabel(), [], $admin->getTranslationDomain())
                    . ' - '
                    . $translator->trans('ROLE_' . $role_item, [], $admin->getTranslationDomain());

                if ($isMaster) {
                    // if the user has the MASTER permission, allow to grant access the admin roles to other users
                    $roles[$role] = $role_label;
                } elseif ($this->authorizationChecker->isGranted($role)) {
                    // although the user has no MASTER permission, allow the currently logged in user to view the role
                    $rolesReadOnly[$role] = $role_label;
                }
            }
        }

        return $roles;
    }
}
