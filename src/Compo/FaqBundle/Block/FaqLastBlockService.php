<?php

namespace Compo\FaqBundle\Block;

use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritdoc}
 */
class FaqLastBlockService extends AbstractBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $container = $this->getContainer();

        $manager = $container->get('compo_faq.manager.faq');

        $settigs = $blockContext->getSettings();
        $block = $blockContext->getBlock();
        $template = $blockContext->getTemplate();

        $publications = $manager->findLastPublications($settigs['limit']);

        return $this->renderResponse(
            $template,
            [
                'faq' => $publications,
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
                    ['limit', 'integer', ['required' => true]],
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
                'limit' => 5,
                'template' => 'CompoFaqBundle:Block:faq_last.html.twig',
            ]
        );
    }
}
