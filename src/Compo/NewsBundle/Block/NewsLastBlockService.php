<?php

namespace Compo\NewsBundle\Block;

use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritdoc}
 */
class NewsLastBlockService extends AbstractBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $container = $this->getContainer();

        $manager = $container->get('compo_news.manager.news');

        $settigs = $blockContext->getSettings();
        $block = $blockContext->getBlock();
        $template = $blockContext->getTemplate();

        $publications = $manager->findLastPublications($settigs['limit']);

        return $this->renderResponse(
            $template,
            array(
                'news' => $publications,
                'block' => $block,
                'settings' => $settigs,
            ),
            $response
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add(
            'settings',
            'sonata_type_immutable_array',
            array(
                'keys' => array(
                    array('limit', 'integer', array('required' => true)),
                ),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'limit' => 5,
                'template' => 'CompoNewsBundle:Block:news_last.html.twig',
            )
        );
    }
}
