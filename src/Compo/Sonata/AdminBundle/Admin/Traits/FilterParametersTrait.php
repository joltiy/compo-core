<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Admin\Traits;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\HttpFoundation\Request;

/**
 * Исправляет фильтрацию для MANY_TO_MANY.
 */
trait FilterParametersTrait
{
    /**
     * getFilterParameters.
     *
     * @return array
     */
    public function getFilterParameters()
    {
        $parameters = parent::getFilterParameters();

        // build the values array
        if ($this->hasRequest()) {
            /** @var Request $request */
            $request = $this->getRequest();

            $parentAssociationMapping = $this->getParentAssociationMapping();

            // always force the parent value
            if ($parentAssociationMapping && $this->isChild()) {
                /** @var \Sonata\AdminBundle\Admin\AbstractAdmin $parent */
                $parent = $this->getParent();

                $name = str_replace('.', '__', $parentAssociationMapping);

                $idParameter = $parent->getIdParameter();

                $parameters[$name] = ['value' => $request->get($idParameter)];

                if (ClassMetadataInfo::MANY_TO_MANY === $this->getParentAssociationMappingType()) {
                    $parameters[$name] = ['value' => [$request->get($idParameter)]];
                }
            }
        }

        return $parameters;
    }
}
