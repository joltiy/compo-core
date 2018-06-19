<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\PageCodeBundle\Block;

use Compo\PageCodeBundle\Entity\PageCode;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritdoc}
 */
class PageCodeBlockService extends AbstractBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        $block = $blockContext->getBlock();
        $template = $blockContext->getTemplate();

        $list = $this->getDoctrineManager()->getRepository(PageCode::class)->findBy(
            ['enabled' => true, 'layout' => $settings['layout']],
            ['position' => 'asc']
        );

        return $this->renderResponse(
            $template,
            [
                'list' => $list,
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
        $formMapper->add(
            'settings',
            'sonata_type_immutable_array',
            [
                'keys' => [
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
                'template' => 'CompoPageCodeBundle:Block:page_code.html.twig',
                'layout' => '',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys(BlockInterface $block)
    {
        $keys = parent::getCacheKeys($block);

        $cache = $this->getContainer()->get('cache.app');

        $updatedAtCache = $cache->getItem('page_code_updated_at');

        if ($updatedAtCache->isHit()) {
            $updatedAt = $updatedAtCache->get();
        } else {
            $updatedAtTime = new \DateTime();
            $updatedAt = $updatedAtTime->format('U');

            $updatedAtCache->set($updatedAt);

            $cache->save($updatedAtCache);
        }

        $keys['updated_at'] = $updatedAt;

        $keys['layout'] = $block->getSetting('layout', '');

        $keys['block_id'] = '';

        return $keys;
    }
}
