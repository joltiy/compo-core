<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Block;

use Compo\CountryBundle\Entity\Country;
use Compo\FeedbackBundle\Entity\Feedback;
use Compo\ManufactureBundle\Entity\Manufacture;
use Compo\ManufactureBundle\Entity\ManufactureCollection;
use Compo\OrderBundle\Entity\Order;
use Compo\ProductBundle\Entity\Product;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Compo\SupplierBundle\Entity\Supplier;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class BaseAdminStatsBlockService extends AbstractBlockService
{
    public function getEntityChoices()
    {
        return array(
            'Заказы' => Order::class,
            'Товары' => Product::class,
            'Производители' => Manufacture::class,
            'Коллекции' => ManufactureCollection::class,
            'Страны' => Country::class,
            'Поставщики' => Supplier::class,
            'Обратная связь' => Feedback::class,

        );
    }

    public function getFieldsChoices($entity = null)
    {
        $entityChoices = $this->getEntityChoices();

        $fieldsChoices = array();

        $translator = $this->getContainer()->get('translator');

        $adminPool = $this->getContainer()->get('sonata.admin.pool');

        $em = $this->getContainer()->get('doctrine')->getManager();

        foreach ($entityChoices as $entityChoiceKey => $entityChoice) {
            if ($entity && $entityChoice != $entity) {
                continue;
            }
            $admin = $adminPool->getAdminByClass($entityChoice);
            $translation_domain = $admin->getTranslationDomain();


            $classMetadata = $em->getClassMetadata($entityChoice);
            $associationMappings = $classMetadata->associationMappings;
            $fieldsMappings = $classMetadata->fieldMappings;


            foreach ($fieldsMappings as $fieldsMapping) {
                $fieldsChoices[$fieldsMapping['fieldName']] =
                    $entityChoiceKey . ' / ' . $translator->trans('form.label_' . $fieldsMapping['columnName'], array(), $translation_domain);
            }

            foreach ($associationMappings as $associationMapping) {
                $mapplingFieldName = $associationMapping['fieldName'];
                $mapplingAdmin = $adminPool->getAdminByClass($associationMapping['targetEntity']);


                if (!$mapplingAdmin) {
                    continue;
                }

                $mapplingTranslationDomain = $mapplingAdmin->getTranslationDomain();


                $classMetadataMapping = $em->getClassMetadata($associationMapping['targetEntity']);
                $fieldsMappingsMapping = $classMetadataMapping->fieldMappings;

                foreach ($fieldsMappingsMapping as $fieldsMappingAssoc) {
                    $fieldsChoices[$mapplingFieldName . '.' . $fieldsMappingAssoc['fieldName']] =
                        $entityChoiceKey . ' / '
                        . $translator->trans('form.label_' . $this->camelCaseToUnderscore($mapplingFieldName), array(), $translation_domain) . ' / '
                        . $translator->trans('form.label_' . $this->camelCaseToUnderscore($fieldsMappingAssoc['fieldName']), array(), $mapplingTranslationDomain);
                }
            }
        }

        return $fieldsChoices;
    }

    public function applyDimensions($qb, &$dimensions, $associationMappings, $fieldsMappings, $classMetadata, &$joins) {

        foreach ($dimensions as $dimensionItemKey => $dimensionItem) {
            $dimension = $dimensionItem['field'];
            $dimension_name = str_replace('.', '___', $dimension);

            $dimensions[$dimensionItemKey]['code_name'] = $dimension_name;

            if (strpos($dimension, '.') !== false) {
                $dimension_parts = explode('.', $dimension);
                $dimensions[$dimensionItemKey]['label_name'] = $this->camelCaseToUnderscore($dimension_parts[0]);

                $associationMapping = $associationMappings[$dimension_parts[0]];

                $associationTargetClass = $classMetadata->getAssociationTargetClass($dimension_parts[0]);

                $on = '';

                foreach ($associationMapping['sourceToTargetKeyColumns'] as $source => $target) {
                    $on = 'entity.' . $dimension_parts[0] . ' = ' . $dimension_parts[0] . '_join';
                }


                if (!in_array($associationTargetClass, $joins)) {
                    $joins[] = $associationTargetClass;
                    $qb->leftJoin($associationTargetClass, $dimension_parts[0] . '_join', 'WITH', $on);
                }

                $qb->addSelect(str_replace($dimension_parts[0], $dimension_parts[0] . '_join', $dimension) . ' as ' . $dimension_name);
                $qb->addGroupBy($dimension_name);
                $dimensions[$dimensionItemKey]['field_type'] = 'string';

            } else {
                $dimensions[$dimensionItemKey]['label_name'] = $this->camelCaseToUnderscore($dimension);


                if (isset($fieldsMappings[$dimension]) && $fieldsMappings[$dimension]['type'] === 'datetime') {
                    $dimensions[$dimensionItemKey]['field_type'] = 'datetime';
                    $qb->addSelect('DATE_FORMAT(entity.' . $dimension . ', \'%d.%m.%Y\') as ' . $dimension);

                    $qb->addSelect('UNIX_TIMESTAMP(DATE_FORMAT(entity.' . $dimension . ', \'%Y-%m-%d\')) as ' . $dimension . '_raw');

                    $qb->addGroupBy($dimension);

                } else {
                    $dimensions[$dimensionItemKey]['field_type'] = 'string';

                    $qb->addSelect('entity.' . $dimension . ' as ' . $dimension);

                    $qb->addGroupBy($dimension);

                }
            }
        }
    }

    public function applyMetrics($qb, &$metrics, $associationMappings, $classMetadata, $joins) {

        foreach ($metrics as $metricItemKey => $metricItem) {
            $metric = $metricItem['field'];
            $metric_name = str_replace('.', '___', $metric) . '___' . $metricItem['aggregation'];
            $metrics[$metricItemKey]['code_name'] = $metric_name;

            if (strpos($metric,'.') !== false) {
                $metric_parts = explode('.', $metric);
                $metrics[$metricItemKey]['label_name'] = $this->camelCaseToUnderscore($metric_parts[0]);

                $associationMapping = $associationMappings[$metric_parts[0]];

                $associationTargetClass = $classMetadata->getAssociationTargetClass($metric_parts[0]);

                $on = '';

                foreach ($associationMapping['sourceToTargetKeyColumns'] as $source => $target) {
                    $on = 'entity.' . $source . ' = ' . $metric_parts[0] . '_join';
                }

                if (!in_array($associationTargetClass, $joins)) {
                    $joins[] = $associationTargetClass;
                    $qb->leftJoin($associationTargetClass, $metric_parts[0] . '_join', 'WITH', $on);
                }

                $qb->addSelect($metricItem['aggregation'] . '('.$metric.') as ' . $metric_name);
            } else {
                $metrics[$metricItemKey]['label_name'] = $this->camelCaseToUnderscore($metric);

                $qb->addSelect($metricItem['aggregation'] . '('.'entity.' . $metric.') as ' . $metric_name);
            }
        }

        return $qb;
    }

    function camelCaseToUnderscore($input)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    public function createDimensions($settings, $fieldsChoices, $mapping = true)
    {
        if ($settings->has('dimensions')) {

            $settings->remove('dimensions');
        }

        $settings->add('dimensions', \Sonata\AdminBundle\Form\Type\CollectionType::class, array(
            'attr' => array('class' => 'form-stats-dimensions'),
            'label' => 'Группировки',

            'mapped' => $mapping,
            'allow_add' => true,
            'allow_delete' => true,
            'entry_type' => ImmutableArrayType::class,
            'entry_options' => array('keys' => array(

                array('field', ChoiceType::class, array(
                    'attr' => array('class' => 'form-stats-dimensions-field'),

                    'label' => 'Имя',
                    'multiple' => false,
                    'choices' => array_keys($fieldsChoices),
                    'choice_label' => function ($value, $key, $index) use ($fieldsChoices) {
                        return $fieldsChoices[$value];
                    },
                )),

                array('label', 'text', array('label' => 'Заголовок', 'required' => false)),
            )),
        ));

    }

    public function applyFormEvents($formMapper, $formModifier) {

        $formMapper->getFormBuilder()->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();

                $formModifier($event->getForm(), $data);
            }
        );

        $formMapper->getFormBuilder()->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $data = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $data);
            }
        );

        $formMapper->getFormBuilder()->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $data = $event->getForm()->getData();

                if (!$data) {
                    return;
                }

                $settings = $data->getSettings();

                if (isset($settings['dimensions'])) {
                    $fields = array();

                    foreach ($settings['dimensions'] as $settingKey => $settingValue) {
                        if (in_array($settingValue['field'], $fields)) {
                            unset($settings['dimensions'][$settingKey]);
                        }
                        $fields[] = $settingValue['field'];
                    }
                }

                if (isset($settings['metrics'])) {
                    $fields = array();

                    foreach ($settings['metrics'] as $settingKey => $settingValue) {
                        if (in_array($settingValue['field'] . $settingValue['aggregation'], $fields)) {
                            unset($settings['metrics'][$settingKey]);
                        }
                        $fields[] = $settingValue['field'] . $settingValue['aggregation'];
                    }
                }



                $event->getForm()->get('settings')->setData($settings);

            }
        );

        $formMapper->getFormBuilder()->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $event->stopPropagation();
        }, 900);
    }

    public function createMetrics($settings, $fieldsChoices, $mapping = true)
    {
        if ($settings->has('metrics')) {
            $settings->remove('metrics');
        }

        $settings->add('metrics', \Sonata\AdminBundle\Form\Type\CollectionType::class, array(
            'attr' => array('class' => 'form-stats-metrics'),
            'mapped' => $mapping,
            'label' => 'Метрики',

            'allow_add' => true,
            'allow_delete' => true,
            'entry_type' => ImmutableArrayType::class,
            'entry_options' => array('keys' => array(
                array('field', ChoiceType::class, array(
                    'attr' => array('class' => 'form-stats-metrics-field'),

                    'label' => 'Имя',
                    'multiple' => false,
                    'choices' => array_keys($fieldsChoices),
                    'choice_label' => function ($value, $key, $index) use ($fieldsChoices) {
                        return $fieldsChoices[$value];
                    },
                )),

                array('label', 'text', array('label' => 'Заголовок', 'required' => false)),

                array('aggregation', 'choice', array(
                    'label' => 'Агрегация',
                    'choices' => array(
                        'Кол-во' => 'COUNT',
                        'Сумма' => 'SUM',
                        'Среднее арифметическое' => 'AVG',
                        'Минимальное' => 'MIN',
                        'Максимальное' => 'MAX',
                    )
                )),
            )),
        ));

    }
}
