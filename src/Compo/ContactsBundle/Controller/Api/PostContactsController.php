<?php

namespace Compo\ContactsBundle\Controller\Api;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


/**
 * @REST\RouteResource("dispatch")
 */
class PostContactsController extends Controller
{

    use \Compo\CoreBundle\Traits\JsonTrait;


    /**
     * Save contacts
     *
     * @REST\Route(requirements={"_format"="json|xml"})
     * @return View
     *
     *
     */
    public function postAction(Request $request)
    {

        $response = null;

        if ($request->isXmlHttpRequest()) {

           $request_params = $this->getJsonParams($request);
           $response = $this->buildJsonResponse($request_params);



         }



        return View::create(array('sent' => $request_params), 200);
    }

}
