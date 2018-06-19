<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\PageBundle\Listener;

use Sonata\PageBundle\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * {@inheritdoc}
 */
class ExceptionListener extends \Sonata\PageBundle\Listener\ExceptionListener
{
    /**
     * Handles a kernel exception.
     *
     * @param GetResponseForExceptionEvent $event
     *
     * @throws \Exception
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->getException() instanceof InternalErrorException) {
            $this->handleInternalError($event);
        } else {
            $this->handleNativeError($event);
        }
    }

    /**
     * Handles an internal error.
     *
     * @param GetResponseForExceptionEvent $event
     */
    private function handleInternalError(GetResponseForExceptionEvent $event)
    {
        $content = $this->templating->render(
            'SonataPageBundle::internal_error.html.twig',
            [
                'exception' => $event->getException(),
            ]
        );

        $event->setResponse(new Response($content, 500));
    }

    /**
     * Handles a native error.
     *
     * @param GetResponseForExceptionEvent $event
     *
     * @throws mixed
     */
    private function handleNativeError(GetResponseForExceptionEvent $event)
    {
        if (true === $this->debug) {
            return;
        }

        if (true === $this->status) {
            return;
        }

        $this->status = true;

        $exception = $event->getException();
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

        $cmsManager = $this->cmsManagerSelector->retrieve();

        $decoratorStrategy = $this->decoratorStrategy;

        $request = $event->getRequest();

        $_route = $request->get('_route');

        if ($_route && !$decoratorStrategy->isRouteNameDecorable($_route)) {
            return;
        }

        if (!$decoratorStrategy->isRouteUriDecorable($request->getPathInfo())) {
            return;
        }

        if (!$this->hasErrorCode($statusCode)) {
            return;
        }

        $message = sprintf('%s: %s (uncaught exception) at %s line %s', \get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine());

        $this->logException($exception, $exception, $message);

        try {
            $page = $this->getErrorCodePage($statusCode);

            $cmsManager->setCurrentPage($page);

            $locale = $page->getSite()->getLocale();

            if ($locale !== $request->getLocale()) {
                // Compare locales because Request returns the default one if null.

                // If 404, LocaleListener from HttpKernel component of Symfony is not called.
                // It uses the "_locale" attribute set by SiteSelectorInterface to set the request locale.
                // So in order to translate messages, force here the locale with the site.
                $request->setLocale($locale);
            }

            $response = $this->pageServiceManager->execute($page, $request, [], new Response('', $statusCode));
        } catch (\Exception $e) {
            $this->logException($exception, $e);

            $event->setException($e);
            $this->handleInternalError($event);

            return;
        }

        $event->setResponse($response);
    }

    /**
     * Logs exceptions.
     *
     * @param \Exception  $originalException  Original exception that called the listener
     * @param \Exception  $generatedException Generated exception
     * @param string|null $message            Message to log
     */
    private function logException(\Exception $originalException, \Exception $generatedException, $message = null)
    {
        if (!$message) {
            $message = sprintf('Exception thrown when handling an exception (%s: %s)', \get_class($generatedException), $generatedException->getMessage());
        }

        $logger = $this->logger;

        if (null !== $logger) {
            if (!$originalException instanceof HttpExceptionInterface || $originalException->getStatusCode() >= 500) {
                $logger->critical($message, ['exception' => $originalException]);
            } else {
                $logger->error($message, ['exception' => $originalException]);
            }
        } else {
            /* @noinspection ForgottenDebugOutputInspection */
            error_log($message);
        }
    }
}
