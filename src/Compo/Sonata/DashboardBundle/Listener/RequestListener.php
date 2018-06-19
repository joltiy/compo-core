<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\DashboardBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
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
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
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
        if ('sonata_admin_dashboard' === $event->getRequest()->get('_route')) {
            $settingsManager = $this->container->get('sylius.settings_manager');

            $settings = $settingsManager->load('compo_dashboard');

            $admin = $this->container->get('sonata.dashboard.admin.dashboard');

            $modelManager = $admin->getModelManager();

            if (!$settings['default']) {
                return;
            }

            $defaultDashboard = $modelManager->findOneBy(
                $admin->getClass(),
                ['id' => $settings['default']]
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
