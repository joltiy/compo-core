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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class DateStatsAdminBlockService extends BaseAdminStatsBlockService
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

        $stats = array();


        $timeTodayFrom = new \DateTime();
        $timeTodayFrom->setTime(0,0,0);

        $timeTodayTo = new \DateTime();
        $timeTodayTo->setTime(23,59,59);

        $qb = $repository->createQueryBuilder('entity');
        $qb->resetDQLPart('select');

        $joins = array();
        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata, $joins);
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
        $joins = array();
        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata, $joins);
        $qb->addSelect("'yesterday' as period");

        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));
        $stats[] = $qb->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);



        $timeTodayFrom = new \DateTime('last Monday');
        $timeTodayFrom->setTime(0,0,0);

        $timeTodayTo = new \DateTime('Sunday');
        $timeTodayTo->setTime(23,59,59);

        $qb = $repository->createQueryBuilder('entity');
        $qb->resetDQLPart('select');
        $joins = array();
        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata, $joins);
        $qb->addSelect("'week' as period");

        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));

        $stats[] = $qb->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);


        $timeTodayFrom = new \DateTime('last Monday');
        $timeTodayFrom->modify('-1 week');
        $timeTodayFrom->setTime(0,0,0);

        $timeTodayTo = new \DateTime('Sunday');
        $timeTodayTo->modify('-1 week');
        $timeTodayTo->setTime(23,59,59);

        $qb = $repository->createQueryBuilder('entity');
        $qb->resetDQLPart('select');
        $joins = array();
        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata, $joins);
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
        $joins = array();
        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata, $joins);
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

        $joins = array();
        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata, $joins);

        $qb->addSelect("'previousMonth' as period");

        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));

        $stats[] = $qb->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

        $qb = $repository->createQueryBuilder('entity');

        $qb->resetDQLPart('select');
        $joins = array();
        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata, $joins);
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



    /**
     * {@inheritdoc}
     */
    public function buildForm(FormMapper $formMapper, BlockInterface $block)
    {
        $entityChoices = $this->getEntityChoices();

        $formMapper->add(
            'settings',
            'sonata_type_immutable_array',
            array(
                'keys' => array(
                    array('tableVisible', CheckboxType::class, array(
                        'label' => 'Таблица',
                        'required' => false,
                        'sonata_help' => 'Вывод таблицы'
                    )),
                    array('chart', CheckboxType::class, array(
                        'label' => 'График',
                        'required' => false,
                        'sonata_help' => 'Вывод графика'
                    )),
                    array('entity', ChoiceType::class, array(
                        'attr' => array('class' => 'form-stats-entity'),
                        'choices' => $entityChoices,
                        'required' => true
                    )),
                ),
            )
        );

        $formModifier = function (FormInterface $form, $data = null) {
            if ($data) {
                $settings = $form->get('settings');

                $settings_data = $data->getSettings();

                $request_entity = $this->getRequest()->get('entity');

                if ($request_entity) {
                    $fieldsChoices = $this->getFieldsChoices($request_entity);
                } elseif (isset($settings_data['entity'])) {
                    $entity = $settings_data['entity'];
                    $fieldsChoices = $this->getFieldsChoices($entity);
                } else {
                    $entitys = $this->getEntityChoices();

                    $fieldsChoices = $this->getFieldsChoices(array_shift($entitys));
                }

                $this->createMetrics($settings, $fieldsChoices);

                $isGetDimensions = $this->getRequest() ? $this->getRequest()->get('get_dimensions', false) : false;

                if ($isGetDimensions) {
                    $this->createMetrics($form, $fieldsChoices, false);
                }
            }
        };

        $this->applyFormEvents($formMapper, $formModifier);
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
