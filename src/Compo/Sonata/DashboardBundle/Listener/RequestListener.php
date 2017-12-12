<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\DashboardBundle\Listener;

use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * This class redirect the onKernelRequest event
 * to the correct DashboardAdminController action.
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RequestListener
{
    /**
     * @var Container
     */
    private $container;

    /**
     * Constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Filter the `kernel.request` event to catch the dashboardAction.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequest()->get('_route') === 'sonata_admin_dashboard') {
            $settingsManager = $this->container->get('sylius.settings_manager');

            $settings = $settingsManager->load('compo_dashboard');

            $admin = $this->container->get('sonata.dashboard.admin.dashboard');

            $modelManager = $admin->getModelManager();

            if (!$settings['default']) {
                return;
            }

            $defaultDashboard = $modelManager->findOneBy(
                $admin->getClass(),
                array('id' => $settings['default'])
            );

            if (!$defaultDashboard) {
                return;
            }

            if ($defaultDashboard) {
                $url = $admin->generateObjectUrl('render', $defaultDashboard);
                $event->setResponse(new RedirectResponse($url));
            }
        }
    }
}
