<?php

namespace Compo\AdvantagesBundle\Block;

use Compo\AdvantagesBundle\Entity\AdvantagesItemRepository;
use Compo\AdvantagesBundle\Entity\AdvantagesRepository;
use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritDoc}
 */
class AdvantagesBlockService extends AbstractBlockService
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $em = $this->getContainer()->get("doctrine")->getManager();

        $settings = $blockContext->getSettings();

        /** @var AdvantagesItemRepository $repository */
        $repository = $em->getRepository("CompoAdvantagesBundle:AdvantagesItem");

        $list = array();

        if ($settings['id']) {
            $list = $repository->findBy(array('advantages' => $settings['id']));
        }

        return $this->renderResponse($blockContext->getTemplate(), array(
            'list' => $list,
            'block' => $blockContext->getBlock(),
            'settings' => $blockContext->getSettings(),
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormMapper $formMapper, BlockInterface $block)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $repository = $em->getRepository('CompoAdvantagesBundle:Advantages');

        $choices = $repository->getChoices();

        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('id', 'choice', array('choices' => $choices, 'label' => 'Приемущества')),
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'id' => null,
            'template' => 'CompoAdvantagesBundle:Block:advantages.html.twig',
        ));
    }

    public function getCacheKeys(BlockInterface $block)
    {
        $settings = $block->getSettings();

        if (isset($settings['id'])) {
            $em = $this->getContainer()->get("doctrine")->getManager();

            /** @var AdvantagesRepository $repository */
            $repository = $em->getRepository("CompoAdvantagesBundle:Advantages");

            $advantages = $repository->find($settings['id']);

            return array(
                'block_id' => "CompoAdvantagesBundle:Advantages:" . $settings['id'],
                'updated_at' => $advantages->getUpdatedAt()->format('U'),
            );
        } else {
            return parent::getCacheKeys($block);
        }

    }
}
