<?php

namespace Compo\Sonata\PageBundle\Event;

use Doctrine\Common\Persistence\ObjectManager;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * {@inheritdoc}
 */
class SitemapPageSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param ObjectManager         $manager
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, ObjectManager $manager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'registerItems',
        );
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function registerItems(SitemapPopulateEvent $event)
    {
        $posts = $this->manager->getRepository('CompoSonataPageBundle:Page')->findBy(
            array(
                'routeName' => 'page_slug',
            )
        );

        foreach ($posts as $post) {
            $event->getUrlContainer()->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'page_slug',
                        array('path' => $post->getUrl()),
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    $post->getUpdatedAt()
                ),
                'page'
            );
        }
    }
}
