<?php

namespace Compo\Sonata\BlockBundle\Block\Service;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritdoc}
 */
class TextBlockService extends \Sonata\BlockBundle\Block\Service\TextBlockService
{
    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'content' => '',
                'template' => 'SonataBlockBundle:Block:block_core_text.html.twig',
            ]
        );
    }
}
