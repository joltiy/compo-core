<?php

namespace Compo\SeoBundle\Command;

use Compo\SeoBundle\Entity\SeoPage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class SeoPageLoadCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compo:seo_page:load')
            ->setDescription('Hello PhpStorm');
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
            if (!$repository->findBy(array('context' => $item['context']))) {
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