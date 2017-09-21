<?php

namespace Compo\CoreBundle\Block;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\FormatterBundle\Block\FormatterBlockService;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritdoc}
 */
class Error404BlockService extends FormatterBlockService
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'format' => 'richhtml',
                'rawContent' => '<b>404</b>',
                'content' => '<b>404</b>',
                'template' => 'CompoCoreBundle:Block:error_404.html.twig',
            )
        );
    }
}
