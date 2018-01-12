<?php

namespace Compo\SocialBundle\Block;

use Compo\SocialBundle\Entity\SocialRepository;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
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
}
