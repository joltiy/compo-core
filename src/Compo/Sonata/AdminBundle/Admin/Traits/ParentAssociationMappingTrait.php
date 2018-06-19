<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Admin\Traits;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;

/**
 * Исправлеят получение родительской админки, для связей MANY_TO_ONE/ONE_TO_ONE/MANY_TO_MANY.
 */
trait ParentAssociationMappingTrait
{
    /**
     * @var array
     */
    protected $childAdminMenuItems = [];

    /**
     * @var
     */
    protected $parentAdminMenuItem;

    /**
     * @return array
     */
    public function getChildAdminMenuItems(): array
    {
        return $this->childAdminMenuItems;
    }

    /**
     * @param $childAdminMenuItem
     */
    public function addChildAdminMenuItem($childAdminMenuItem): void
    {
        $this->childAdminMenuItems[$childAdminMenuItem] = $childAdminMenuItem;
    }

    /**
     * @param $childAdminMenuItem
     *
     * @return bool
     */
    public function hasChildAdminMenuItem($childAdminMenuItem): bool
    {
        if (isset($this->childAdminMenuItems[$childAdminMenuItem])) {
            return true;
        }

        return false;
    }

    /**
     * @param array $childAdminMenuItems
     */
    public function setChildAdminMenuItems(array $childAdminMenuItems): void
    {
        $this->childAdminMenuItems = $childAdminMenuItems;
    }

    /**
     * @return mixed
     */
    public function getParentAdminMenuItem()
    {
        return $this->parentAdminMenuItem;
    }

    /**
     * @param mixed $parentAdminMenuItem
     */
    public function setParentAdminMenuItem($parentAdminMenuItem): void
    {
        $this->parentAdminMenuItem = $parentAdminMenuItem;
    }

    /**
     * @param $parentAssociationMapping
     */
    public function setParentParentAssociationMapping($parentAssociationMapping)
    {
        $key = 'parentAssociationMapping';

        $this->{$key} = $parentAssociationMapping;
    }

    /**
     * @return null|string
     */
    public function getParentAssociationMappingType()
    {
        $type = null;

        $mm = $this->getModelManager();

        if ($mm instanceof ModelManager) {
            $associations = $this->getParentAssociations();

            if (\count($associations)) {
                $first = reset($associations);

                if (false !== $first) {
                    $type = $first['type'];
                }
            }
        }

        return $type;
    }

    /**
     * @return array
     */
    public function getParentAssociations()
    {
        $mm = $this->getModelManager();
        if ($mm instanceof ModelManager && $this->isChild()) {
            /** @var AbstractAdmin $parent */
            $parent = $this->getParent();

            // Get associations from this entity to the parent entity (if any)
            return $mm->getMetadata($this->getClass())->getAssociationsByTargetClass($parent->getClass());
        }

        return [];
    }

    /**
     * @return null|string
     */
    public function getParentAssociationMapping()
    {
        $name = null;

        $mm = $this->getModelManager();

        if ($mm instanceof ModelManager) {
            $associations = $this->getParentAssociations();

            foreach ($associations as $association) {
                // When this admin is child the association must be of the following types
                switch ($association['type']) {
                    case ClassMetadataInfo::MANY_TO_ONE:
                    case ClassMetadataInfo::ONE_TO_ONE:
                        $name = $association['fieldName'];

                        return $name;
                        break;
                    case ClassMetadataInfo::MANY_TO_MANY:
                        $name = $association['fieldName'];
                        break;
                }
            }
        }

        return $name;
    }
}
