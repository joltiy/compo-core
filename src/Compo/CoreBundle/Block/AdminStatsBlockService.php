<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Block;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class AdminStatsBlockService extends BaseAdminStatsBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();

        $entityClass = $settings['entity'];

        $container = $this->getContainer();

        $em = $container->get('doctrine')->getManager();

        /** @var EntityRepository $repository */
        $repository = $em->getRepository($entityClass);

        $qb = $repository->createQueryBuilder('entity');
        $qb->select('COUNT(entity.id)');
        $total = $qb->getQuery()->getSingleScalarResult();

        $timeTodayFrom = new \DateTime();
        $timeTodayFrom->setTime(0, 0, 0);

        $timeTodayTo = new \DateTime();
        $timeTodayTo->setTime(23, 59, 59);

        $qb = $repository->createQueryBuilder('entity');
        $qb->select('COUNT(entity.id)');
        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));
        $today = $qb->getQuery()->getSingleScalarResult();

        $timeTodayFrom = new \DateTime();
        $timeTodayFrom->setTime(0, 0, 0);
        $timeTodayFrom->modify('-1 day');

        $timeTodayTo = new \DateTime();
        $timeTodayTo->setTime(23, 59, 59);
        $timeTodayTo->modify('-1 day');

        $qb = $repository->createQueryBuilder('entity');
        $qb->select('COUNT(entity.id)');
        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));
        $yesterday = $qb->getQuery()->getSingleScalarResult();

        $timeTodayFrom = new \DateTime('last Monday');
        $timeTodayFrom->setTime(0, 0, 0);

        $timeTodayTo = new \DateTime('Sunday');
        $timeTodayTo->setTime(23, 59, 59);

        $qb = $repository->createQueryBuilder('entity');
        $qb->select('COUNT(entity.id)');
        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));
        $week = $qb->getQuery()->getSingleScalarResult();

        $timeTodayFrom = new \DateTime('last Monday');
        $timeTodayFrom->modify('-1 week');
        $timeTodayFrom->setTime(0, 0, 0);

        $timeTodayTo = new \DateTime('Sunday');
        $timeTodayTo->modify('-1 week');
        $timeTodayTo->setTime(23, 59, 59);

        $qb = $repository->createQueryBuilder('entity');
        $qb->select('COUNT(entity.id)');
        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));
        $previousWeek = $qb->getQuery()->getSingleScalarResult();

        $timeTodayFrom = new \DateTime();
        $timeTodayFrom->setDate($timeTodayFrom->format('Y'), $timeTodayFrom->format('m'), 1);
        $timeTodayFrom->setTime(0, 0, 0);

        $timeTodayTo = new \DateTime(date('Y-m-t'));
        $timeTodayTo->setTime(23, 59, 59);

        $qb = $repository->createQueryBuilder('entity');
        $qb->select('COUNT(entity.id)');
        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));
        $month = $qb->getQuery()->getSingleScalarResult();

        $timeTodayFrom = new \DateTime();
        $timeTodayFrom->setDate($timeTodayFrom->format('Y'), $timeTodayFrom->format('m'), 1);

        $timeTodayFrom->setTime(0, 0, 0);
        $timeTodayFrom->modify('-1 month');

        $timeTodayTo = new \DateTime(date('Y-m-t'));
        $timeTodayTo->setTime(23, 59, 59);
        $timeTodayTo->modify('-1 month');

        $qb = $repository->createQueryBuilder('entity');
        $qb->select('COUNT(entity.id)');
        $qb->where('entity.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $timeTodayFrom->format('Y-m-d H:i:s'))
            ->setParameter('to', $timeTodayTo->format('Y-m-d H:i:s'));
        $previousMonth = $qb->getQuery()->getSingleScalarResult();

        $stats = [
            'total' => $total,
            'today' => $today,
            'yesterday' => $yesterday,
            'week' => $week,
            'previousWeek' => $previousWeek,
            'month' => $month,
            'previousMonth' => $previousMonth,
        ];

        $admin = $container->get('sonata.admin.pool')->getAdminByClass($entityClass);

        if (!$admin) {
            throw new \RuntimeException('Not found admin by class: ' . $entityClass);
        }

        $url = $admin->generateUrl('list');

        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'url' => $url,
                'stats' => $stats,
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
                    ['entity', ChoiceType::class, [
                        'choices' => $entityChoices,
                        'required' => true,
                    ]],
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'entity' => '',
                'template' => 'CompoCoreBundle:Block:admin_stats.html.twig',
            ]
        );
    }
}
