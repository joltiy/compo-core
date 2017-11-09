<?php

namespace Compo\RedirectBundle\Listener;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\RedirectBundle\Entity\Redirect;
use Compo\RedirectBundle\Repository\RedirectRepository;
use Compo\Sonata\PageBundle\Entity\Page;
use Sonata\PageBundle\Exception\PageNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RedirectListener
 * @package Compo\RedirectBundle\Listener
 */
class RedirectListener
{
    use ContainerAwareTrait;

    public $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param GetResponseEvent $event
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
            array(
                'urIn' => array($uri, $pathInfo),
                'enabled' => true
            )
        );

        // TODO: Опция, не учитывать параметры в исходном/конечном URL

        if ($redirect) {
            $event->setResponse(new RedirectResponse($baseUrl . $redirect->getUrOut()));
            $event->stopPropagation();
        }

        $pageRepository = $em->getRepository(Page::class);

        $page = $pageRepository->findOneBy(
            array(
                'url' => array( $pathInfo . '/')
            )
        );

        if ($page) {
            $event->setResponse(new RedirectResponse($baseUrl . $pathInfo . '/'));
            $event->stopPropagation();
        }


    }
}
