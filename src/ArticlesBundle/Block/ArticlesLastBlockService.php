<?php

namespace Compo\ArticlesBundle\Block;

use Compo\ArticlesBundle\Repository\ArticlesRepository;
use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritdoc}
 */
class ArticlesLastBlockService extends AbstractBlockService
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $em = $this->getContainer()->get("doctrine")->getManager();

        /** @var ArticlesRepository $repository */
        $repository = $em->getRepository("CompoArticlesBundle:Articles");

        $publications = $repository->findLastPublications();

        return $this->renderResponse($blockContext->getTemplate(), array(
            'articles' => $publications,
            'block' => $blockContext->getBlock(),
            'settings' => $blockContext->getSettings(),
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('class', 'text', array('required' => false)),
                array('template', 'text', array('required' => false)),
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'class' => '',
            'template' => 'CompoArticlesBundle:Block:articles_last.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata('Последнии статьи', (!is_null($code) ? $code : $this->getName()), false, 'SonataBlockBundle', array(
            'class' => 'fa fa-file-text-o',
        ));
    }
}
