<?php

namespace Compo\SeoBundle\Command;

use Compo\SeoBundle\Entity\SeoPage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@inheritdoc}
 */
class SeoPageLoadCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compo:seo:page:load')
            ->setDescription('Seo pages load');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $manager = $container->get('compo_seo.page.manager');
        $items = $manager->getSeoPages();

        $em = $container->get('doctrine')->getManager();

        $repository = $em->getRepository('CompoSeoBundle:SeoPage');

        foreach ($items as $key => $item) {
            if (!$repository->findBy(['context' => $item['context']])) {
                $seoPage = new SeoPage();

                $seoPage->setContext($item['context']);
                $seoPage->setHeader($item['header']);
                $seoPage->setDescription($item['description']);
                $seoPage->setDescriptionAdditional($item['descriptionAdditional']);
                $seoPage->setTitle($item['title']);
                $seoPage->setMetaKeyword($item['metaKeyword']);
                $seoPage->setMetaDescription($item['metaDescription']);

                $em->persist($seoPage);
                $em->flush();
            }
        }
    }
}
