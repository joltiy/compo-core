<?php

namespace Compo\ArticlesBundle\Block;

use Compo\ArticlesBundle\Repository\ArticlesRepository;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritdoc}
 */
class ArticlesLastBlockService extends AbstractBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $container = $this->getContainer();

        $articlesManager = $container->get("compo_articles.manager.articles");

        $settigs = $blockContext->getSettings();
        $block = $blockContext->getBlock();
        $template = $blockContext->getTemplate();
        $publications = $articlesManager->findLastPublications($settigs['limit']);

        return $this->renderResponse($template, array(
            'articles' => $publications,
            'block' => $block,
            'settings' => $settigs,
        ), $response);
    }

    /**
     * @param FormMapper $formMapper
     * @param BlockInterface $block
     */
    public function buildForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('limit', 'integer', array('required' => true)),
            ),
        ));
    }


    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'limit' => 5,
            'template' => 'CompoArticlesBundle:Block:articles_last.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata('block.title_articles_last', $this->getName(), false, 'CompoArticlesBundle', array(
            'class' => 'fa fa-file-text-o',
        ));
    }
}
