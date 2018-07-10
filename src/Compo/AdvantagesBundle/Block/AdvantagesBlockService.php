<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\AdvantagesBundle\Block;

use Compo\AdvantagesBundle\Entity\Advantages;
use Compo\AdvantagesBundle\Entity\AdvantagesItem;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritdoc}
 */
class AdvantagesBlockService extends AbstractBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();

        $block = $blockContext->getBlock();

        $em = $this->getDoctrineManager();

        $advantagesRepository = $em->getRepository(Advantages::class);

        $advantages = $advantagesRepository->find($settings['id']);

        $repository = $em->getRepository(AdvantagesItem::class);

        $list = $repository->findBy(
            [
                'advantages' => $settings['id'],
                'enabled' => true,
            ],
            [
                'position' => 'asc',
            ]
        );

        return $this->renderResponse(
            $settings['template'],
            [
                'advantages' => $advantages,
                'list' => $list,
                'context' => $blockContext,
                'block' => $block,
                'settings' => $settings,
            ],
            $response
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormMapper $formMapper, BlockInterface $block)
    {
        $em = $this->getDoctrineManager();

        $repository = $em->getRepository(Advantages::class);

        $choices = $repository->getChoices();

        $formMapper->add(
            'settings',
            'sonata_type_immutable_array',
            [
                'keys' => [
                    [
                        'id',
                        'choice',
                        [
                            'choices' => $choices,
                            'label' => 'form.label_advantages',
                            'translation_domain' => 'CompoAdvantagesBundle',
                        ],
                    ],
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
                'id' => null,
                'template' => 'CompoAdvantagesBundle:Block:advantages.html.twig',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys(BlockInterface $block)
    {
        $keys = parent::getCacheKeys($block);

        $keys['updated_at'] = $this->getContainer()->get('compo_core.manager')->getUpdatedAtCacheAsString(Advantages::class);

        return $keys;
    }
}
