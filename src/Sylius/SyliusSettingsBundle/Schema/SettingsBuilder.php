<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Schema;

use Sylius\Bundle\SettingsBundle\Transformer\ParameterTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SettingsBuilder extends OptionsResolver implements SettingsBuilderInterface
{
    /**
     * @var ParameterTransformerInterface[]
     */
    protected $transformers = [];

    /**
     * {@inheritdoc}
     */
    public function getTransformers()
    {
        return $this->transformers;
    }

    /**
     * {@inheritdoc}
     */
    public function setTransformer($parameterName, ParameterTransformerInterface $transformer)
    {
        $this->transformers[$parameterName] = $transformer;
    }
}
