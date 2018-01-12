<?php

namespace Compo\CoreBundle\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class JsonTrait.
 */
trait JsonTrait
{
    /**
     * @param Request $request
     *
     * @return array|mixed
     */
    public function getJsonParams(Request $request)
    {
        $request_params = [];

        if (0 === mb_strpos($request->headers->get('Content-Type'), 'application/json')) {
            $request_params = json_decode($request->getContent(), true);
        }

        return $request_params;
    }

    /**
     * @param array $data
     *
     * @return JsonResponse
     */
    public function buildJsonResponse(array $data)
    {
        $jsonResponse = new JsonResponse();
        $jsonResponse->setData($data);

        return $jsonResponse;
    }
}
