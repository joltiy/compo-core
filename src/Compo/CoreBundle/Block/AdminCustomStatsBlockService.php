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
use Doctrine\ORM\AbstractQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class AdminCustomStatsBlockService extends BaseAdminStatsBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();

        $entityClass = $settings['entity'];
        $dimensions = $settings['dimensions'];
        $metrics = $settings['metrics'];

        $container = $this->getContainer();
        $translator = $container->get('translator');

        $admin = $container->get('sonata.admin.pool')->getAdminByClass($entityClass);

        $translation_domain = $admin->getTranslationDomain();

        $em = $container->get('doctrine')->getManager();

        /** @var EntityRepository $repository */
        $repository = $em->getRepository($entityClass);

        $qb = $repository->createQueryBuilder('entity');

        $timeTodayFrom = new \DateTime();
        $timeTodayFrom->setDate($timeTodayFrom->format('Y'), $timeTodayFrom->format('m'), 1);
        $timeTodayFrom->setTime(0, 0, 0);

        $timeTodayTo = new \DateTime(date('Y-m-t'));
        $timeTodayTo->setTime(23, 59, 59);

        $request = $container->get('request_stack')->getCurrentRequest();

        if ($settings['period']) {
            $fromDate = $request->get('from_date', $settings['fromDate']);
            $toDate = $request->get('to_date', $settings['toDate']);

            if ($fromDate && $toDate) {
                $timeTodayFrom = new \DateTime($fromDate);

                $timeTodayTo = new \DateTime($toDate);

                $qb->where('entity.createdAt BETWEEN :from AND :to')
                    ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
                    ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));
            } else {
                $qb->where('entity.createdAt BETWEEN :from AND :to')
                    ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
                    ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));
            }
        }

        $classMetadata = $em->getClassMetadata($entityClass);
        $associationMappings = $classMetadata->associationMappings;
        $fieldsMappings = $classMetadata->fieldMappings;

        $qb->resetDQLPart('select');

        $joins = [];

        $this->applyDimensions($qb, $dimensions, $associationMappings, $fieldsMappings, $classMetadata, $joins);

        $this->applyMetrics($qb, $metrics, $associationMappings, $classMetadata, $joins);

        $result = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        $dimensionsKeys = [];

        foreach ($dimensions as $dimension) {
            $dimensionsKeys[] = $dimension['field'];
        }

        foreach ($result as $resultKey => $resultItem) {
            foreach ($resultItem as $key => $value) {
                if (is_object($value) && $value instanceof \DateTime) {
                    $result[$resultKey][$key] = $value->format('d.m.Y');

                    $result[$resultKey][$key . '_raw'] = $value->getTimestamp();
                }

                if (in_array($key, $dimensionsKeys)) {
                    $result[$resultKey][$key] = $translator->trans($value, [], $translation_domain);
                }
            }
        }

        $url = $admin->generateUrl('list');

        $form = $container->get('form.factory')->createNamed('date_range_form_' . $blockContext->getBlock()->getId(), 'Symfony\Component\Form\Extension\Core\Type\FormType', [
            'fromDate' => $timeTodayFrom,
            'toDate' => $timeTodayTo,
        ])
            ->add('fromDate', DatePickerType::class, [
                'format' => 'dd.MM.y',
                'attr' => ['class' => 'from-date-input'],
            ])
            ->add('toDate', DatePickerType::class, [
                'format' => 'dd.MM.y',
                'attr' => ['class' => 'to-date-input'],
            ]);

        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'date_range_form' => $form->createView(),
                'dimensions' => $dimensions,
                'metrics' => $metrics,

                'translation_domain' => $admin->getTranslationDomain(),
                'url' => $url,
                'stats' => $result,
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
            ],
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
            [
                'keys' => [
                    ['tableVisible', CheckboxType::class, [
                        'label' => 'Таблица',
                        'required' => false,
                        'sonata_help' => 'Вывод таблицы',
                    ]],
                    ['pagination', CheckboxType::class, [
                        'label' => 'Постраничная навигация',
                        'required' => false,
                        'sonata_help' => 'Вывод постраничной навигации, поиска',
                    ]],

                    ['chart', CheckboxType::class, [
                        'label' => 'График',
                        'required' => false,
                        'sonata_help' => 'Вывод графика',
                    ]],
                    ['timeline', CheckboxType::class, [
                        'label' => 'По дате',
                        'required' => false,
                        'sonata_help' => 'Вывод графика по дате (первая группировка должна быть датой)',
                    ]],
                    ['period', CheckboxType::class, [
                        'label' => 'За период',
                        'required' => false,
                        'sonata_help' => 'Выборка и группировка по дате создания за период',
                    ]],

                    ['entity', ChoiceType::class, [
                        'attr' => ['class' => 'form-stats-entity'],
                        'choices' => $entityChoices,
                        'required' => true,
                    ]],
                ],
            ]
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

                $this->createDimensions($settings, $fieldsChoices);
                $this->createMetrics($settings, $fieldsChoices);

                $isGetDimensions = $this->getRequest() ? $this->getRequest()->get('get_dimensions', false) : false;

                if ($isGetDimensions) {
                    $this->createDimensions($form, $fieldsChoices, false);
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
            [
                'fromDate' => false,
                'toDate' => false,
                'pagination' => false,

                'tableVisible' => true,
                'period' => false,

                'chart' => false,
                'timeline' => false,

                'entity' => '',
                'dimensions' => [],
                'metrics' => [],

                'template' => 'CompoCoreBundle:Block:admin_custom_stats.html.twig',
            ]
        );
    }
}
