<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
            [
                'format' => 'richhtml',
                'rawContent' => '<b>404</b>',
                'content' => '<b>404</b>',
                'template' => 'CompoCoreBundle:Block:error_404.html.twig',
            ]
        );
    }
}
