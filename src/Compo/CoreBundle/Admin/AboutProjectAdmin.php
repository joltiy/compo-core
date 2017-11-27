<?php

namespace Compo\CoreBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class SettingsAdmin
 *
 * @package Compo\CoreBundle\Admin
 */
class AboutProjectAdmin extends AbstractAdmin
{

    protected $baseRouteName = 'compo_core_about_project';
    protected $baseRoutePattern = '/compo_core_about_project';

}
