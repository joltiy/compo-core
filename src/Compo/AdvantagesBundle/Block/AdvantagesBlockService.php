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

        $list = array();

        if ($settings['id']) {
            $list = $repository->findBy(array('advantages' => $settings['id'], 'enabled' => true), array('position' => 'asc'));
        }

        return $this->renderResponse(
            $settings['template'],
            array(
                'list' => $list,
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
        $repository = $this->getDoctrineManager()->getRepository(Advantages::class);

        $choices = $repository->getChoices();

        $formMapper->add(
            'settings',
            'sonata_type_immutable_array',
            array(
                'keys' => array(
                    array('id', 'choice', array('choices' => $choices, 'label' => 'Приемущества')),
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
                'id' => null,
                'template' => 'CompoAdvantagesBundle:Block:advantages.html.twig',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys(BlockInterface $block)
    {
        $settings = $block->getSettings();

        $keys = parent::getCacheKeys($block);

        $keys['environment'] = $this->getContainer()->get('kernel')->getEnvironment();

        if (isset($settings['id'])) {
            $repository = $this->getDoctrineManager()->getRepository(Advantages::class);

            $item = $repository->find($settings['id']);

            if ($item) {
                $key = $this->getName() . ':' . $settings['id'];

                if (isset($settings['template'])) {
                    $key = $key . ':' . $settings['template'];
                }

                $keys['block_id'] = $key;
                $keys['updated_at'] = $item->getUpdatedAt()->format('U');
            }
        }

        return $keys;
    }
}
