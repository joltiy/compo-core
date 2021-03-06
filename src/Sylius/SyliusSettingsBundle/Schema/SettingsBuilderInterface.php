<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Schema;

use Sylius\Bundle\SettingsBundle\Transformer\ParameterTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SettingsBuilderInterface extends OptionsResolverInterface
{
    /**
     * @return ParameterTransformerInterface[]
     */
    public function getTransformers();

    /**
     * @param string                        $parameterName
     * @param ParameterTransformerInterface $transformer
     */
    public function setTransformer($parameterName, ParameterTransformerInterface $transformer);

    /**
     * @param $parameterName
     * @param $types
     *
     * @return mixed
     */
    public function addAllowedTypes($parameterName, $types);

    /**
     * @param array $defaults
     *
     * @return mixed
     */
    public function setDefaults(array $defaults);
}
