<?php

declare(strict_types=1);

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\DashboardBundle\Controller;

use Compo\Sonata\AdminBundle\Controller\CRUDController;
use Compo\Sonata\DashboardBundle\Entity\Block;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Dashboard Admin Controller.
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
class DashboardAdminController extends CRUDController
{
    /**
     * @param Request $request
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function renderAction(Request $request)
    {
        /*
        $this->admin->checkAccess('compose');
        if (false === $this->get('sonata.dashboard.admin.block')->isGranted('LIST')) {
            throw $this->createAccessDeniedException();
        }
        */

        $admin = $this->getAdmin();

        $id = $request->get($admin->getIdParameter());

        $dashboard = $admin->getObject($id);

        if (!$dashboard) {
            throw $this->createNotFoundException(sprintf('unable to find the dashboard with id : %s', $id));
        }

        $containers = [];

        /** @var array $blocks */
        $blocks = $dashboard->getBlocks();

        // separate containers.
        foreach ($blocks as $block) {
            $containers[] = $block;
        }

        return $this->renderWithExtraParams('@CompoSonataDashboard/Dashboard/render.html.twig', [
            'action' => 'render',
            'admin' => $admin,
            'object' => $dashboard,

            'dashboard' => $dashboard,
            'containers' => $containers,
        ]);
    }

    /**
     * @param Request $request
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function composeAction(Request $request)
    {
        $admin = $this->getAdmin();

        $admin->checkAccess('compose');
        if (false === $this->get('sonata.dashboard.admin.block')->isGranted('LIST')) {
            throw $this->createAccessDeniedException();
        }

        $id = $request->get($admin->getIdParameter());
        $dashboard = $admin->getObject($id);
        if (!$dashboard) {
            throw $this->createNotFoundException(sprintf('unable to find the dashboard with id : %s', $id));
        }

        $containers = [];

        // separate containers.
        /** @var array $blocks */
        $blocks = $dashboard->getBlocks();

        foreach ($blocks as $block) {
            /** @var Block $block */
            $blockCode = $block->getSetting('code');
            if (null === $block->getParent()) {
                $containers[$blockCode]['block'] = $block;
            }
        }

        return $this->renderWithExtraParams($template = $admin->getTemplate('compose'), [
            'object' => $dashboard,
            'action' => 'edit',
            'containers' => $containers,
            'csrfTokens' => [
                'remove' => $this->getCsrfToken('sonata.delete'),
            ],
        ]);
    }

    /**
     * @param Request $request
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function composeContainerShowAction(Request $request)
    {
        if (false === $this->get('sonata.dashboard.admin.block')->isGranted('LIST')) {
            throw $this->createAccessDeniedException();
        }
        $admin = $this->getAdmin();

        $id = $request->get($admin->getIdParameter());
        $block = $this->get('sonata.dashboard.admin.block')->getObject($id);
        if (!$block) {
            throw $this->createNotFoundException(sprintf('unable to find the block with id : %s', $id));
        }

        $blockServices = $this->get('sonata.block.manager')->getServicesByContext('sonata_dashboard_bundle', false);

        return $this->renderWithExtraParams($template = $admin->getTemplate('compose_container_show'), [
            'blockServices' => $blockServices,
            'container' => $block,
            'dashboard' => $block->getDashboard(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @throws \Twig\Error\Error
     *
     * @return Response
     */
    public function renderBlockAction(Request $request)
    {
        if (false === $this->get('sonata.dashboard.admin.block')->isGranted('LIST')) {
            throw $this->createAccessDeniedException();
        }

        $id = $request->get($this->admin->getIdParameter());

        $block = $this->get('sonata.dashboard.admin.block')->getObject($id);

        if (!$block) {
            throw $this->createNotFoundException(sprintf('unable to find the block with id : %s', $id));
        }

        $content = $this->container->get('templating')->render('@CompoSonataDashboard/Dashboard/single_block.html.twig', [
            'block' => $block,
            'request' => $request,
        ]);

        $response = new Response();

        $response->setContent($content);

        return $response;
    }
}
