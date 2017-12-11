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
use Sonata\BlockBundle\Block\BlockContextInterface;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class DateStatsAdminBlockService extends AbstractBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();

        $entityClass = $settings['entity'];
        $metrics = $settings['metrics'];

        $container = $this->getContainer();

        $admin = $container->get('sonata.admin.pool')->getAdminByClass($entityClass);

        $em = $container->get('doctrine')->getManager();

        /** @var EntityRepository $repository */
        $repository = $em->getRepository($entityClass);

        $classMetadata = $em->getClassMetadata($entityClass);
        $associationMappings = $classMetadata->associationMappings;
        $fieldsMappings = $classMetadata->fieldMappings;

        $stats = array();


        $timeTodayFrom = new \DateTime();
        $timeTodayFrom->setTime(0,0,0);

        $timeTodayTo = new \DateTime();
        $timeTodayTo->setTime(23,59,59);

        $qb = $repository->createQueryBuilder('entity');
        $qb->resetDQLPart('select');
        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata);
        $qb->addSelect("'today' as period");

        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));
        $stats[] = $qb->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);


        $timeTodayFrom = new \DateTime();
        $timeTodayFrom->setTime(0,0,0);
        $timeTodayFrom->modify('-1 day');

        $timeTodayTo = new \DateTime();
        $timeTodayTo->setTime(23,59,59);
        $timeTodayTo->modify('-1 day');

        $qb = $repository->createQueryBuilder('entity');
        $qb->resetDQLPart('select');
        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata);
        $qb->addSelect("'yesterday' as period");

        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));
        $stats[] = $qb->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);



        $timeTodayFrom = new \DateTime('Monday');
        $timeTodayFrom->setTime(0,0,0);

        $timeTodayTo = new \DateTime('Sunday');
        $timeTodayTo->setTime(23,59,59);

        $qb = $repository->createQueryBuilder('entity');
        $qb->resetDQLPart('select');
        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata);
        $qb->addSelect("'week' as period");

        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));

        $stats[] = $qb->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);


        $timeTodayFrom = new \DateTime('Monday');
        $timeTodayFrom->modify('-1 week');
        $timeTodayFrom->setTime(0,0,0);

        $timeTodayTo = new \DateTime('Sunday');
        $timeTodayTo->modify('-1 week');
        $timeTodayTo->setTime(23,59,59);

        $qb = $repository->createQueryBuilder('entity');
        $qb->resetDQLPart('select');
        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata);
        $qb->addSelect("'previousWeek' as period");

        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));

        $stats[] = $qb->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

        $timeTodayFrom = new \DateTime();
        $timeTodayFrom->setDate($timeTodayFrom->format('Y'), $timeTodayFrom->format('m'), 1);
        $timeTodayFrom->setTime(0,0,0);

        $timeTodayTo = new \DateTime(date("Y-m-t"));
        $timeTodayTo->setTime(23,59,59);

        $qb = $repository->createQueryBuilder('entity');
        $qb->resetDQLPart('select');
        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata);
        $qb->addSelect("'month' as period");

        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));
        $stats[] = $qb->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);


        $timeTodayFrom = new \DateTime();
        $timeTodayFrom->setDate($timeTodayFrom->format('Y'), $timeTodayFrom->format('m'), 1);

        $timeTodayFrom->setTime(0,0,0);
        $timeTodayFrom->modify('-1 month');

        $timeTodayTo = new \DateTime(date("Y-m-t"));
        $timeTodayTo->setTime(23,59,59);
        $timeTodayTo->modify('-1 month');

        $qb = $repository->createQueryBuilder('entity');
        $qb->resetDQLPart('select');
        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata);
        $qb->addSelect("'previousMonth' as period");

        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));

        $stats[] = $qb->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

        $qb = $repository->createQueryBuilder('entity');

        $qb->resetDQLPart('select');
        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata);
        $qb->addSelect("'total' as period");

        $stats[] = $qb->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);


        $url = $container->get('sonata.admin.pool')->getAdminByClass($entityClass)->generateUrl('list');

        return $this->renderResponse(
            $blockContext->getTemplate(),
            array(
                'metrics' => $metrics,
                'dimensions' => array(
                    'period' => array(
                        'label_name' => 'Период',
                        'label' => 'Период',
                        'field_type' => 'string',
                        'code_name' => 'period',
                    )
                ),

                'translation_domain' => $admin->getTranslationDomain(),
                'url' => $url,
                'stats' => $stats,
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
            ),
            $response
        );
    }


    public function applyMetrics($qb, &$metrics, $associationMappings, $classMetadata) {
        $joins = array();

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

                    array('entity', ChoiceType::class, array(
                        'choices' => array(
                            'Заказы' => Order::class,
                            'Товары' => Product::class
                        ),
                        'required' => true
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
                'timeline' => false,

                'chart' => false,
                'entity' => '',
                'metrics' => array(),
                'dimensions' => array(),

                'template' => 'CompoCoreBundle:Block:date_stats_admin.html.twig',
            )
        );
    }
}
