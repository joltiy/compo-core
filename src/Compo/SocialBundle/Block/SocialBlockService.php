<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\SocialBundle\Block;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\SocialBundle\Entity\SocialRepository;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SocialBlockService extends AbstractBlockService
{
    use ContainerAwareTrait;


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $em = $this->container->get("doctrine")->getManager();

        $settings = $blockContext->getSettings();

        /** @var SocialRepository $repo */
        $repo = $em->getRepository("CompoSocialBundle:Social");

        $list = $repo->findBy(array(), array('position' => 'ASC'));

        return $this->renderResponse($blockContext->getTemplate(), array(

            'list' => $list,
            'block' => $blockContext->getBlock(),
            'settings' => $blockContext->getSettings(),
        ), $response);
    }


    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'template' => 'CompoSocialBundle:Block:list.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata('Соц.аккаунты', (!is_null($code) ? $code : $this->getName()), false, 'SonataBlockBundle', array(
            'class' => 'fa fa-file-text-o',
        ));
    }
}
