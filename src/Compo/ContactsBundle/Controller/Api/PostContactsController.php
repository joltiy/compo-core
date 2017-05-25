<?php

namespace Compo\ContactsBundle\Controller\Api;


use Compo\ContactsBundle\Entity\Feedback;
use Compo\ContactsBundle\Form\FeedbackFormType;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as REST;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PostContactsController extends Controller
{

    use \Compo\CoreBundle\Traits\JsonTrait;


    /**
     * Validates contact form
     * Saves contact entity
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
     *
     *  }
     * )
     *
     * @param Request $request Request represents an HTTP request.
     *
     * @return View
     *
     * @throws \HttpRequestMethodException
     */
    public function postAction(Request $request)
    {

        $response = [];


        if (!$request->isXmlHttpRequest())
            throw new \HttpRequestMethodException();


        $request_params = $this->getJsonParams($request);


        $feedback = new Feedback();
        $form = $this->createForm(new FeedbackFormType(), $feedback);
        $form->submit($request_params['data']);
        $csrf = $this->get('security.csrf.token_manager');


        if (!$form->isValid()) {
            $csrf->refreshToken('feedback_protection');

            return View::create(array('success' => false, 'error' => 'form_not_valid'), 400);

        } else {


            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($feedback);
                $em->flush();
                $csrf->refreshToken('feedback_protection');
                $this->sendMessage($feedback);
                return View::create(array('success' => true, 'message' => 'contacts_sent'), 200);


            } catch (\Exception $e) {
                return View::create(array('success' => false, 'error' => $e->getMessage()), 400);
            }


        }


    }


    private function sendMessage(Feedback $entity)
    {
        $settings = $this->container->get('sylius.settings.manager')->load('compo_core_settings');
        $email_from = $settings->get('notification_email_from');
        $email_to = $settings->get('notification_email');

        $message = \Swift_Message::newInstance()
            ->setSubject('Сообщение из формы обратной связи ' . $entity->getPage())
            ->setFrom($email_from)
            ->setTo($email_to)
            ->setBody(
                $this->renderView(
                    '@CompoContacts/Emails/contactform.html.twig',
                    array('data' => $entity)
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $message = \Swift_Message::newInstance()
            ->setSubject('Спасибо за обращение')
            ->setFrom($email_from)
            ->setTo($entity->getEmail())
            ->setBody(
                $this->renderView(
                    '@CompoContacts/Emails/clientnotice.html.twig',
                    array('data' => $entity)
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

    }


}
