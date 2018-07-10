<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\BannerBundle\Block;

use Compo\BannerBundle\Entity\Banner;
use Compo\BannerBundle\Entity\BannerItem;
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
        $em = $this->getEntityManager();

        $settings = $blockContext->getSettings();

        $bannerItemRepository = $em->getRepository(BannerItem::class);

        $bannerRepository = $em->getRepository(Banner::class);

        $list = [];

        $banner = null;

        if ($settings['id']) {
            $banner = $bannerRepository->find($settings['id']);

            if ($banner) {
                $list = $bannerItemRepository->findBy(['banner' => $banner, 'enabled' => true], ['position' => 'asc']);
            }
        }

        return $this->renderResponse(
            $settings['template'],
            [
                'banner' => $banner,
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
        $em = $this->getContainer()->get('doctrine')->getManager();

        $repository = $em->getRepository(Banner::class);

        /** @var Banner[] $list */
        $list = $repository->getChoices();

        $formMapper->add(
            'settings',
            'sonata_type_immutable_array',
            [
                'keys' => [
                    [
                        'id',
                        'choice',
                        [
                            'choices' => $list,
                            'label' => 'form.label_banner'
                        ]
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
                'template' => 'CompoBannerBundle:Block:slider.html.twig',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys(BlockInterface $block)
    {
        $keys = parent::getCacheKeys($block);

        $keys['updated_at'] = $this->getContainer()->get('compo_core.manager')->getUpdatedAtCacheAsString(Banner::class);

        return $keys;
    }
}
