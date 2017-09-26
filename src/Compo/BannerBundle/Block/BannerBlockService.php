<?php

namespace Compo\BannerBundle\Block;

use Compo\BannerBundle\Entity\Banner;
use Compo\BannerBundle\Entity\BannerItemRepository;
use Compo\BannerBundle\Entity\BannerRepository;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class BannerBlockService extends AbstractBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $settings = $blockContext->getSettings();

        /** @var BannerItemRepository $repo */
        $repo = $em->getRepository('CompoBannerBundle:BannerItem');

        $list = array();

        if ($settings['id']) {
            $list = $repo->findBy(array('banner' => $settings['id'], 'enabled' => true), array('position' => 'asc'));
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
     * {@inheritDoc}
     */
    public function buildForm(FormMapper $formMapper, BlockInterface $block)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $menuRepository = $em->getRepository('CompoBannerBundle:Banner');

        /** @var Banner[] $list */
        $list = $menuRepository->getChoices();

        $formMapper->add(
            'settings',
            'sonata_type_immutable_array',
            array(
                'keys' => array(
                    array('id', 'choice', array('choices' => $list, 'label' => 'Баннеры')),
                    array('template', 'choice', array(
                        'choices' => array(
                            'Обычный' => 'CompoBannerBundle:Block:slider.html.twig',
                            'Менеджеры' => 'CompoBannerBundle:Block:managers.html.twig'
                        ),
                        'label' => 'Шаблон'
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
                'id' => null,
                'template' => 'CompoBannerBundle:Block:slider.html.twig',
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getCacheKeys(BlockInterface $block)
    {
        $settings = $block->getSettings();

        $keys = parent::getCacheKeys($block);

        $keys['environment'] = $this->getContainer()->get('kernel')->getEnvironment();

        if (isset($settings['id'])) {
            $em = $this->getContainer()->get('doctrine')->getManager();

            /** @var BannerRepository $repository */
            $repository = $em->getRepository('CompoBannerBundle:Banner');

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
