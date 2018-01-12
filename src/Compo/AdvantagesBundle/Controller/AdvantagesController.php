<?php

namespace Compo\AdvantagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdvantagesController extends Controller
{
    public function renderAction(Request $request, $id)
    {
        $advantagesManager = $this->get('compo_advantages.manager.advantages');

        $blockHelper = $this->get('sonata.block.templating.helper');

        $response = new Response();

        $response->setETag(md5($advantagesManager->getUpdatedAt()->format('U')));
        $response->setLastModified($advantagesManager->getUpdatedAt());
        $response->setSharedMaxAge(601);
        $response->setMaxAge(601);

        // Set response as public. Otherwise it will be private by default.
        $response->setPublic();

        // Check that the Response is not modified for the given Request
        if ($response->isNotModified($request)) {
            // return the 304 Response immediately
            return $response;
        }

        $response->setContent(
            $blockHelper->render([
                'type' => 'compo_advantages.block.service.advantages',
                'settings' => [
                    'id' => $id,
                ],
            ])
        );

        return $response;
    }
}
