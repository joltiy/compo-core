<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\AdvantagesBundle\Block;

use Compo\AdvantagesBundle\Entity\AdvantagesItemRepository;
use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\AdvantagesFactory;
use Knp\Menu\AdvantagesItem;
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
class AdvantagesBlockService extends AbstractBlockService
{
    use ContainerAwareTrait;


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $em = $this->container->get("doctrine.orm.entity_manager");

        $settings = $blockContext->getSettings();

        /** @var AdvantagesItemRepository $repo */
        $repo = $em->getRepository("CompoAdvantagesBundle:AdvantagesItem");

        if($settings['id']) {
            $list = $repo->findBy(array('advantages' => $settings['id']));
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

        $menuRepository = $em->getRepository('CompoAdvantagesBundle:Advantages');

        $list = $menuRepository->findAll();

        $choices = array();

        foreach ($list as $item) {
            $choices[$item->getId()] = $item->getName();
        }

        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('id', 'choice', array('choices' => $choices, 'label' => 'Приемущества')),

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

            'template' => 'CompoAdvantagesBundle:Block:advantages.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata('Приемущества', (!is_null($code) ? $code : $this->getName()), false, 'SonataBlockBundle', array(
            'class' => 'fa fa-file-text-o',
        ));
    }
}
