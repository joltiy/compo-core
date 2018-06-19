<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ImportBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;

/**
 * {@inheritdoc}
 */
class ImportExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        $collection->add('import', 'import', [
            '_controller' => 'CompoImportBundle:Default:index',
        ]);
        $collection->add('upload', '{import_id}/upload', [
            '_controller' => 'CompoImportBundle:Default:upload',
        ]);
        $collection->add('importStatus', '{import_id}/upload/status', [
            '_controller' => 'CompoImportBundle:Default:importStatus',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureExportFields(AdminInterface $admin, array $fields)
    {
        /** @noinspection SuspiciousAssignmentsInspection */
        $fields = [];

        /** @var FieldDescription[] $elements */
        $elements = $admin->getList()->getElements();

        foreach ($elements as $key => $element) {
            if (\in_array($element->getName(), ['batch', '_action'])) {
                continue;
            }

            if (!$element->getOption('active')) {
                continue;
            }

            if (ClassMetadataInfo::MANY_TO_ONE === $element->getMappingType()) {
                //$fields[$element->getOption('label')] = $element->getName() . '.' . $element->getOption('associated_property', 'id');
                $fields[$element->getName()] = $element->getName();
            } elseif (ClassMetadataInfo::MANY_TO_MANY === $element->getMappingType()) {
                $associationMapping = $element->getAssociationMapping();

                if (method_exists($associationMapping['sourceEntity'], $element->getName() . 'ExportAsString')) {
                    $fields[$element->getName()] = $element->getName() . 'ExportAsString';
                } //else {
                    //$fields[$element->getName()] = $element->getName();
                //}
            } elseif (null === $element->getMappingType()) {
                $fields[$element->getName()] = $element->getName();

                if (method_exists($admin->getClass(), $element->getName() . 'ExportAsString')) {
                    $fields[$element->getName()] = $element->getName() . 'ExportAsString';
                }
            } else {
                $fields[$element->getName()] = $element->getName();
            }
        }

        return $fields;
    }
}
