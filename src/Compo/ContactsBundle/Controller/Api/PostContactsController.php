<?php

namespace Compo\ContactsBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * ContactsApi controller.
 */
class PostContactsController extends Controller
{

    use \Compo\CoreBundle\Traits\JsonTrait;

    public function postAction(Request $request)
    {

        $response = null;

        if ($request->isXmlHttpRequest()) {

           $request_params = $this->getJsonParams($request);
           $response = $this->buildJsonResponse($request_params);



        }

        return $response;
    }

}
