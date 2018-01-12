<?php
/**
 * Created by PhpStorm.
 * User: jivoy1988
 * Date: 25.11.16
 * Time: 14:57.
 */

namespace Compo\CoreBundle\Subscriber;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Event\PaginationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * {@inheritdoc}
 */
class SlidingPaginationSubscriber implements EventSubscriberInterface
{
    /**
     * @var
     */
    private $route;
    /**
     * @var array
     */
    private $params = [];
    /**
     * @var array
     */
    private $options;

    /**
     * SlidingPaginationSubscriber constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'knp_pager.pagination' => ['pagination', 1],
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        $this->route = $request->attributes->get('_route');
        $this->params = array_merge([], $request->attributes->get('_route_params', []));
        foreach ($this->params as $key => $param) {
            if (0 === mb_strpos($key, '_')) {
                unset($this->params[$key]);
            }
        }
    }

    /**
     * @param PaginationEvent $event
     */
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
