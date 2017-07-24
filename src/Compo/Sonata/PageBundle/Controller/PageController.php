<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\PageBundle\Controller;

use Compo\Sonata\PageBundle\Entity\Page;
use Compo\Sonata\PageBundle\Entity\Site;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Page Admin Controller.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class PageController extends \Sonata\PageBundle\Controller\PageController
{


    public function getRequest()
    {

        $request_stack = $this->get('request_stack');

        if ($request_stack) {
            $request =  $request_stack->getCurrentRequest();

            if ($request) {
                return $request;
            } else {
                return new Request();
            }
        } else {
            return new Request();
        }
    }

}
