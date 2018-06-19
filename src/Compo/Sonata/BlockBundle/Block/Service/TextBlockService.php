<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
