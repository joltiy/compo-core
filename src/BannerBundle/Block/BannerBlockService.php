<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\BannerBundle\Block;

use Compo\BannerBundle\Entity\BannerItemRepository;
use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\BannerFactory;
use Knp\Menu\BannerItem;
use Knp\Menu\Renderer\ListRenderer;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class BannerBlockService extends AbstractBlockService
{
    use ContainerAwareTrait;


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $em = $this->container->get("doctrine.orm.entity_manager");

        $settings = $blockContext->getSettings();

        /** @var BannerItemRepository $repo */
        $repo = $em->getRepository("CompoBannerBundle:BannerItem");

        if($settings['id']) {
            $list = $repo->findBy(array('banner' => $settings['id']));
        } else {
            $list = array();
        }


        return $this->renderResponse($blockContext->getTemplate(), array(

            'list' => $list,
            'block' => $blockContext->getBlock(),
            'settings' => $blockContext->getSettings(),
        ), $response);
    }


    public function buildForm(FormMapper $formMapper, BlockInterface $block)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $menuRepository = $em->getRepository('CompoBannerBundle:Banner');

        $list = $menuRepository->findAll();

        $choices = array();

        foreach ($list as $item) {
            $choices[$item->getId()] = $item->getName();
        }

        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('id', 'choice', array('choices' => $choices, 'label' => 'Баннеры')),

            ),
        ));
    }


    /**
     * {@inheritdoc}
     */
    public function buildCreateForm(FormMapper $formMapper, BlockInterface $block)
    {
        $this->buildForm($formMapper, $block);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $this->buildForm($formMapper, $block);
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'id' => null,

            'template' => 'CompoBannerBundle:Block:slider.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata('Баннеры - слайдер', (!is_null($code) ? $code : $this->getName()), false, 'SonataBlockBundle', array(
            'class' => 'fa fa-file-text-o',
        ));
    }
}
