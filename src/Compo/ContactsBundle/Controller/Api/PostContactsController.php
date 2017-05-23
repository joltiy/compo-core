<?php

namespace Compo\ContactsBundle\Controller\Api;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Compo\ContactsBundle\Entity\Feedback;
use Compo\ContactsBundle\Form\FeedbackFormType;


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
                $response['message'] = 'form_not_valid';

            }
            else{

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($feedback);
                    $em->flush();
                    $csrf->refreshToken('feedback_protection');
                    $this->sendMessage($feedback);


                    $response['message'] = 'contacts_sent';
                }





        return View::create($response, 200);
    }



    private function sendMessage(Feedback $entity)
    {
        $settings = $this->container->get('sylius.settings.manager')->load('compo_core_settings');
        $email_from = $settings->get('notification_email_from');
        $email_to = $settings->get('notification_email');

        $message = \Swift_Message::newInstance()
            ->setSubject('Сообщение из формы обратной связи '.$entity->getPage())
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
