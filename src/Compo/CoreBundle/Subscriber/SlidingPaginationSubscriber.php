<?php
/**
 * Created by PhpStorm.
 * User: jivoy1988
 * Date: 25.11.16
 * Time: 14:57
 */

namespace Compo\CoreBundle\Subscriber;


use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Event\PaginationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * {@inheritDoc}
 */
class SlidingPaginationSubscriber implements EventSubscriberInterface
{
    private $route;
    private $params = array();
    private $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'knp_pager.pagination' => array('pagination', 1)
        );
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        $this->route = $request->attributes->get('_route');
        $this->params = array_merge(array(), $request->attributes->get('_route_params', array()));
        foreach ($this->params as $key => $param) {
            if (substr($key, 0, 1) == '_') {
                unset($this->params[$key]);
            }
        }
    }

    public function pagination(PaginationEvent $event)
    {
        // default sort field and order
        $eventOptions = $event->options;

        if (isset($eventOptions['defaultSortFieldName']) && !isset($this->params[$eventOptions['sortFieldParameterName']])) {
            $this->params[$eventOptions['sortFieldParameterName']] = $eventOptions['defaultSortFieldName'];
        }

        if (isset($eventOptions['defaultSortDirection']) && !isset($this->params[$eventOptions['sortDirectionParameterName']])) {
            $this->params[$eventOptions['sortDirectionParameterName']] = $eventOptions['defaultSortDirection'];
        }

        $pagination = new SlidingPagination($this->params);

        $pagination->setUsedRoute($this->route);
        $pagination->setTemplate($this->options['defaultPaginationTemplate']);
        $pagination->setSortableTemplate($this->options['defaultSortableTemplate']);
        $pagination->setFiltrationTemplate($this->options['defaultFiltrationTemplate']);
        $pagination->setPageRange($this->options['defaultPageRange']);

        $event->setPagination($pagination);
        $event->stopPropagation();
    }
}