<?php

namespace Compo\FeedbackBundle\Controller\Api;

use Compo\FeedbackBundle\Entity\Feedback;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * {@inheritDoc}
 */
class FeedbackController extends Controller
{
    use \Compo\CoreBundle\Traits\JsonTrait;

    /**
     *
     * Works with contact form data
     *
     * Validates contact form,
     * Saves contact entity,
     * Sends notification message to user and site administration
     *
     * @REST\Route(requirements={"_format"="json|xml"})
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="$request", "dataType"="Request", "requirement"="Request", "description"="Request represents an HTTP request."}
     *  },
     *  statusCodes={
     *      200="Returned when contacts is successfully saved and notification sent",
     *      400="Returned when an error has occurred while contact form validated or when something went wrong",
     *  }
     * )
     *
     * @param Request $request Request represents an HTTP request.
     *
     * @return View
     *
     * @throws \HttpRequestMethodException
     * @throws \Throwable
     */
    public function postAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new \HttpRequestMethodException('Not isXmlHttpRequest');
        }

        $request_params = $this->getJsonParams($request);

        $feedback = new Feedback();

        $feedbackManager = $this->get('compo_feedback.manager.feedback');
        $feedbackType = $feedbackManager->getType($request_params['data']['type']);

        $form = $this->createForm($feedbackType['form'], $feedback);

        $form->submit($request_params['data']);

        $csrf = $this->get('security.csrf.token_manager');

        if (!$form->isValid()) {
            $csrf->refreshToken('feedback_protection');

            return View::create(array('success' => false, 'error' => 'form_not_valid'), 400);
        }

        try {
            $em = $this->getDoctrine()->getManager();

            $em->persist($feedback);
            $em->flush();

            $csrf->refreshToken('feedback_protection');

            $this->get('compo_notification.manager.notification')->send($feedback->getType() . '_for_user', array('feedback' => $feedback));
            $this->get('compo_notification.manager.notification')->send($feedback->getType() . '_for_admin', array('feedback' => $feedback));

            return View::create(array('success' => true, 'message' => 'contacts_sent'), 200);
        } catch (\Exception $e) {
            return View::create(array('success' => false, 'error' => $e->getMessage()), 400);
        }
    }
}
