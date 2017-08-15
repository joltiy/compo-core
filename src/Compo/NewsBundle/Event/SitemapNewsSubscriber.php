<?php

namespace Compo\NewsBundle\Event;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapNewsSubscriber implements EventSubscriberInterface
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
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'registerItems',
        ];
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function registerItems(SitemapPopulateEvent $event)
    {
        $posts = $this->manager->getRepository('CompoNewsBundle:News')->findAll();

        foreach ($posts as $post) {
            $event->getUrlContainer()->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'compo_news_show_by_slug',
                        ['slug' => $post->getSlug()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    $post->getUpdatedAt()
                ),
                'news'
            );
        }
    }
}