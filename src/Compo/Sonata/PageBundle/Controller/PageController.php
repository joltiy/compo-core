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


use Symfony\Component\HttpFoundation\Request;


/**
 * Page Admin Controller.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class PageController extends \Sonata\PageBundle\Controller\PageController
{


    /**
     * @return null|Request
     */
    public function getRequest()
    {
        $request_stack = $this->get('request_stack');

        if ($request_stack) {
            $request =  $request_stack->getCurrentRequest();

            if ($request) {
                return $request;
            }
        }

        return new Request();
    }

}
