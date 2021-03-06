<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\FormatterBundle\Block;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritdoc}
 */
class FormatterBlockService extends \Sonata\FormatterBundle\Block\FormatterBlockService
{
    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        parent::configureSettings($resolver);

        $resolver->setDefault('rawContent', '');
        $resolver->setDefault('content', '');
    }
}
