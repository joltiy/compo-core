<?php

namespace Compo\ContactsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CompoContactsBundle:Default:index.html.twig');
    }
}
