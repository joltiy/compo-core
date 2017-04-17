<?php

namespace Compo\Sonata\FormatterBundle\Block;

use Symfony\Component\OptionsResolver\OptionsResolver;

class FormatterBlockService extends \Sonata\FormatterBundle\Block\FormatterBlockService
{
    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'format' => 'richhtml',
            'rawContent' => '',
            'content' => '',
            'template' => 'SonataFormatterBundle:Block:block_formatter.html.twig',
        ));
    }
}