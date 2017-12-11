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

use Compo\CoreBundle\Doctrine\ORM\EntityRepository;
use Compo\OrderBundle\Entity\Order;
use Compo\ProductBundle\Entity\Product;
use Doctrine\ORM\AbstractQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class DatePeriodAdminStatsBlockService extends AbstractBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();

        $entityClass = $settings['entity'];
        $dimensions =  $settings['dimensions'];
        $metrics = $settings['metrics'];

        $container = $this->getContainer();

        $admin = $container->get('sonata.admin.pool')->getAdminByClass($entityClass);

        $em = $container->get('doctrine')->getManager();

        /** @var EntityRepository $repository */
        $repository = $em->getRepository($entityClass);

        $qb = $repository->createQueryBuilder('entity');

        $classMetadata = $em->getClassMetadata($entityClass);
        $associationMappings = $classMetadata->associationMappings;
        $fieldsMappings = $classMetadata->fieldMappings;

        $qb->resetDQLPart('select');

        $joins = array();

        foreach ($dimensions as $dimensionItemKey => $dimensionItem) {
            $dimension = $dimensionItem['field'];
            $dimension_name = str_replace('.', '___', $dimension);

            $dimensions[$dimensionItemKey]['code_name'] = $dimension_name;

            if (strpos($dimension,'.') !== false) {
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
                    $qb->addSelect('DATE_FORMAT(entity.' . $dimension . ', \'%Y.%m.%d\') as ' . $dimension);

                    $qb->addSelect('UNIX_TIMESTAMP(DATE_FORMAT(entity.' . $dimension . ', \'%Y-%m-%d\')) as ' . $dimension . '_raw');

                    $qb->addGroupBy($dimension);

                } else {
                    $dimensions[$dimensionItemKey]['field_type'] = 'string';

                    $qb->addSelect('entity.' . $dimension . ' as ' . $dimension);

                    $qb->addGroupBy($dimension);

                }
            }
        }

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

        $result = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        foreach ($result as $resultKey => $resultItem) {

            foreach ($resultItem as $key => $value) {
                if (is_object($value) && $value instanceof \DateTime) {
                    $result[$resultKey][$key] = $value->format('Y.m.d');

                   // $result[$resultKey][$key . '_raw'] = $value->getTimestamp();

                } else {
                    //$result[$resultKey][$key . '_raw'] = '';
                }
            }

        }


        $totalItem = array();




        $url = $admin->generateUrl('list');

        return $this->renderResponse(
            $blockContext->getTemplate(),
            array(
                'dimensions' => $dimensions,
                'metrics' => $metrics,

                'translation_domain' => $admin->getTranslationDomain(),
                'url' => $url,
                'stats' => $result,
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
            ),
            $response
        );
    }

    function camelCaseToUnderscore($input)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add(
            'settings',
            'sonata_type_immutable_array',
            array(
                'keys' => array(
                    array('tableVisible', CheckboxType::class, array('label' => 'Таблица', 'required' => false)),

                    array('chart', CheckboxType::class, array('label' => 'График', 'required' => false)),
                    array('timeline', CheckboxType::class, array('label' => 'По дате', 'required' => false)),

                    array('dimensions', \Sonata\AdminBundle\Form\Type\CollectionType::class, array(
                        'allow_add' => true,
                        'allow_delete' => true,
                        'entry_type' => ImmutableArrayType::class,
                        'entry_options' => array('keys' => array(
                            array('field', 'text', array('label' => 'Имя')),
                            array('label', 'text', array('label' => 'Заголовок', 'required' => false)),
                        )),
                    )),
                    array('metrics', \Sonata\AdminBundle\Form\Type\CollectionType::class, array(
                        'allow_add' => true,
                        'allow_delete' => true,
                        'entry_type' => ImmutableArrayType::class,
                        'entry_options' => array('keys' => array(
                            array('field', 'text', array('label' => 'Имя')),
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
                    )),
                    //array('dimensions', TextType::class, array('required' => true)),
                    //array('metrics', TextType::class, array('required' => true)),

                    array('entity', ChoiceType::class, array(
                        'choices' => array(
                            'Заказы' => Order::class,
                            'Товары' => Product::class
                        ),
                        'required' => true
                    )),
                ),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'tableVisible' => true,

                'chart' => false,
                'timeline' => false,

                'entity' => '',
                'dimensions' => array(),
                'metrics' => array(),

                'template' => 'CompoCoreBundle:Block:admin_custom_stats.html.twig',
            )
        );
    }
}
