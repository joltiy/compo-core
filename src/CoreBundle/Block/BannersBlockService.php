<?php
/**
 * Created by PhpStorm.
 * User: jivoy1988
 * Date: 20.02.17
 * Time: 11:53
 */

namespace Compo\CoreBundle\Block;


use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;

class BannersBlockService extends AbstractBlockService
{
    use ContainerAwareTrait;

    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'url'      => false,
            'title'    => 'Insert the rss title',
            'template' => 'CompoCoreBundle:Block:banners.html.twig',
        ));
    }

    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper
            ->add('settings', 'sonata_type_immutable_array', array(
                'keys' => array(
                    array('url', 'url', array('required' => false)),
                    array('title', 'text', array('required' => false)),
                )
            ))
        ;
    }

    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {

    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // merge settings
        $settings = $blockContext->getSettings();

        return $this->renderResponse($blockContext->getTemplate(), array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings
        ), $response);
    }
}