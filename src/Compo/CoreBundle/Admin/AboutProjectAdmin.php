<?php

namespace Compo\CoreBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;

/**
 * Class SettingsAdmin.
 */
class AboutProjectAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'compo_core_about_project';
    protected $baseRoutePattern = '/compo_core_about_project';
}
