<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ImportBundle\Admin;

use Sonata\AdminBundle\Datagrid\Datagrid;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;

/**
 * Trait ExportFormatsTrait.
 */
trait ExportFormatsTrait
{
    /**
     * @return array
     */
    public function getExportFormats()
    {
        return [
            'xlsx', 'csv', 'xml', 'json',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSourceIterator()
    {
        /** @var Datagrid $datagrid */
        $datagrid = $this->getDatagrid();

        $datagrid->buildPager();

        $fields = [];

        /** @var array $exportFields */
        $exportFields = $this->getExportFields();

        foreach ($exportFields as $key => $field) {
            $transLabel = $this->getExportTranslationLabel($key, $field);

            $fields[$transLabel] = $field;
        }

        /** @var ModelManager $modelManager */
        $modelManager = $this->getModelManager();

        $dataSourceIterator = $modelManager->getDataSourceIterator($datagrid, $fields);

        $dataSourceIterator->setDateTimeFormat('d.m.Y H:i:s');

        return $dataSourceIterator;
    }

    /**
     * @param $key
     * @param $field
     *
     * @return string
     */
    public function getExportTranslationLabel($key, $field)
    {
        $label = $this->getTranslationLabel($field, 'export', 'label');
        $transLabel = $this->trans($label);

        if ($transLabel === $label) {
            $label = $this->getTranslationLabel($field, 'list', 'label');
            $transLabel = $this->trans($label);
        }

        if ($transLabel === $label) {
            $label = $this->getTranslationLabel($key, 'export', 'label');
            $transLabel = $this->trans($label);
        }

        if ($transLabel === $label) {
            $label = $this->getTranslationLabel($key, 'list', 'label');
            $transLabel = $this->trans($label);
        }

        if ($transLabel === $label) {
            $transLabel = $key;
        }

        return $transLabel;
    }
}
