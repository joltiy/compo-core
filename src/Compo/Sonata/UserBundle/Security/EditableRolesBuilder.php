<?php

namespace Compo\Sonata\UserBundle\Security;

/**
 * Class EditableRolesBuilder
 * @package Compo\Sonata\UserBundle\Security
 */
class EditableRolesBuilder extends \Sonata\UserBundle\Security\EditableRolesBuilder
{
    /**
     * @return array
     */
    public function getRoles()
    {
        $roles = array();
        $rolesReadOnly = array();

        if (!$this->tokenStorage->getToken()) {
            return array($roles, $rolesReadOnly);
        }

        $translator = $this->pool->getContainer()->get('translator');



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



            foreach ($admin->getSecurityInformation() as $role => $permissions) {
                $information = $role;
                $role_name = $baseRole;
                $role_name = str_replace('_%s', '', $role_name);




                $role = sprintf($baseRole, $role);

                $role_label = $role;

                if ($information === 'GUEST') {
                    $role_label = $translator->trans($role_name) . ' - Просмотр';
                } elseif ($information === 'STAFF') {
                    $role_label = $translator->trans($role_name) . ' - Редактирование';
                } elseif ($information === 'EDITOR') {
                    $role_label = $translator->trans($role_name) . ' - Действия';
                } elseif ($information === 'ADMIN') {
                    $role_label = $translator->trans($role_name) . ' - Настройки';
                }


                if ($isMaster) {
                    // if the user has the MASTER permission, allow to grant access the admin roles to other users
                    $roles[$role] = $role_label;
                } elseif ($this->authorizationChecker->isGranted($role)) {
                    // although the user has no MASTER permission, allow the currently logged in user to view the role
                    $rolesReadOnly[$role] = $role_label;
                }
            }


        }



        $isMaster = $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN');

        // get roles from the service container
        foreach ($this->rolesHierarchy as $name => $rolesHierarchy) {
            if ($isMaster || $this->authorizationChecker->isGranted($name)) {
                $roles[$name] = $name.': '.implode(', ', $rolesHierarchy);

                foreach ($rolesHierarchy as $role) {
                    if (!isset($roles[$role])) {
                        $roles[$role] = $role;
                    }
                }
            }
        }

        return array($roles, $rolesReadOnly);
    }
}
