<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\RedirectBundle\Listener;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\RedirectBundle\Entity\Redirect;
use Compo\RedirectBundle\Repository\RedirectRepository;
use Compo\Sonata\PageBundle\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RedirectListener.
 */
class RedirectListener
{
    use ContainerAwareTrait;

    /**
     * @var Registry
     */
    public $doctrine;

    /**
     * RedirectListener constructor.
     *
     * @param $doctrine
     */
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws \Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        // /app_dev.php/contacts?5454
        $requestUri = $request->getRequestUri();

        // /contacts
        $pathInfo = $request->getPathInfo();

        // /app_dev.php
        $baseUrl = $request->getBaseUrl();

        $uri = str_replace($baseUrl, '', $requestUri);

        $em = $this->doctrine->getManager();

        /** @var RedirectRepository $redirectRepository */
        $redirectRepository = $em->getRepository('CompoRedirectBundle:Redirect');

        /** @var Redirect $redirect */
        $redirect = $redirectRepository->findOneBy(
            [
                'urIn' => array_unique([$uri, $pathInfo]),
                'enabled' => true,
            ]
        );

        if ($redirect) {
            $event->setResponse(new RedirectResponse($baseUrl . $redirect->getUrOut(), 301));
            $event->stopPropagation();
        }

        $pageRepository = $em->getRepository(Page::class);

        $lastSlash = mb_substr($pathInfo, -1);

        if ('/' !== $lastSlash) {
            $page = $pageRepository->findOneBy(
                [
                    'url' => [$pathInfo . '/'],
                ]
            );

            if ($page) {
                $event->setResponse(new RedirectResponse($baseUrl . $pathInfo . '/', 301));
                $event->stopPropagation();
            }
        }
    }
}
