<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\AdvantagesBundle\Controller;

use Compo\AdvantagesBundle\Entity\Advantages;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * {@inheritdoc}
 */
class AdvantagesController extends Controller
{
    /**
     * @param Request $request
     * @param         $id
     *
     * @return Response
     */
    public function renderAction(Request $request, $id)
    {
        $coreManager = $this->get('compo_core.manager');

        $blockHelper = $this->get('sonata.block.templating.helper');

        $response = new Response();

        $updatedAt = $coreManager->getUpdatedAtCache(Advantages::class);

        $response->setEtag(md5($updatedAt->format('U')));
        $response->setLastModified($updatedAt);
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
