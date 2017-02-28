<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Block;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Model\Metadata;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class NewsWithArticlesLastBlockService extends AbstractBlockService
{
    use ContainerAwareTrait;

    public function postPersist($object)
    {

    }

    public function prePersist($object)
    {

    }

    public function preRemove($object)
    {

    }

    public function postRemove($object)
    {

    }

    public function preUpdate($object)
    {

    }

    public function postUpdate($object)
    {

    }

    

    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        return $this->renderResponse($blockContext->getTemplate(), array(
            'block' => $blockContext->getBlock(),
            'settings' => $blockContext->getSettings(),
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $block->getEnabled();

        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('class', 'text', array('required' => false)),

                array('template', 'text', array('required' => false)),
            ),
        ));
    }

    public function buildCreateForm(FormMapper $formMapper, BlockInterface $block)
    {
        $block->getEnabled();

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

            'template' => 'CompoCoreBundle:Block:news_with_articles_last.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata('Последнии новости и статьи', (!is_null($code) ? $code : $this->getName()), false, 'SonataBlockBundle', array(
            'class' => 'fa fa-file-text-o',
        ));
    }
}
