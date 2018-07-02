<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\SocialBundle\Block;

use Compo\SocialBundle\Entity\Social;
use Compo\SocialBundle\Entity\SocialRepository;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SocialBlockService extends AbstractBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $em = $this->container->get('doctrine')->getManager();

        /** @var SocialRepository $repo */
        $repo = $em->getRepository('CompoSocialBundle:Social');

        $list = $repo->findBy([], ['position' => 'ASC']);

        return $this->renderResponse(
            $blockContext->getTemplate(),
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
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'template' => 'CompoSocialBundle:Block:list.html.twig',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys(BlockInterface $block)
    {
        $keys = parent::getCacheKeys($block);

        $keys['block_id'] = '';

        $keys['updated_at'] = $this->getContainer()->get('compo_core.manager')->getUpdatedAtCacheAsString(Social::class);

        return $keys;
    }
}
