<?php

namespace Compo\PageCodeBundle\Block;

use Compo\PageCodeBundle\Entity\PageCode;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritdoc}
 */
class PageCodeBlockService extends AbstractBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settigs = $blockContext->getSettings();
        $block = $blockContext->getBlock();
        $template = $blockContext->getTemplate();

        $list = $this->getDoctrineManager()->getRepository(PageCode::class)->findBy(
            ['enabled' => true, 'layout' => $settigs['layout']],
            ['position' => 'asc']
        );

        return $this->renderResponse(
            $template,
            [
                'list' => $list,
                'block' => $block,
                'settings' => $settigs,
            ],
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
            [
                'keys' => [
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'template' => 'CompoPageCodeBundle:Block:page_code.html.twig',
                'layout' => '',
            ]
        );
    }
}
