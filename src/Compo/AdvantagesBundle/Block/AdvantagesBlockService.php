<?php

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

        $repository = $this->getDoctrineManager()->getRepository(AdvantagesItem::class);

        $list = [];

        if ($settings['id']) {
            $list = $repository->findBy(['advantages' => $settings['id'], 'enabled' => true], ['position' => 'asc']);
        }

        return $this->renderResponse(
            $settings['template'],
            [
                'list' => $list,
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
        $repository = $this->getDoctrineManager()->getRepository(Advantages::class);

        $choices = $repository->getChoices();

        $formMapper->add(
            'settings',
            'sonata_type_immutable_array',
            [
                'keys' => [
                    ['id', 'choice', ['choices' => $choices, 'label' => 'Приемущества']],
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

        $AdvantagesManager = $this->getContainer()->get('compo_advantages.manager.advantages');

        $keys['updated_at'] = $AdvantagesManager->getUpdatedAt()->format('U');

        return $keys;
    }
}
