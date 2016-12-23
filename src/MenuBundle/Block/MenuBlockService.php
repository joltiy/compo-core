<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\MenuBundle\Block;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Knp\Menu\Renderer\ListRenderer;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class MenuBlockService extends AbstractBlockService
{
    /**
     * @var Container
     */
    public $container;

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param mixed $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {

        $em = $this->container->get("doctrine.orm.entity_manager");

        $settings = $blockContext->getSettings();

        $menu = null;

        /** @var NestedTreeRepository $repo */
        $repo = $em->getRepository("CompoMenuBundle:Menu");


        $tree = $repo->childrenHierarchy($repo->findOneBy(array('alias' => $settings['alias'])), false, array(), false);


        $factory = new MenuFactory();

        $menu = $factory->createItem($settings['alias']);

        $this->renderMenu($menu, $tree);


        $renderer = new ListRenderer(new Matcher());

        return $this->renderResponse($blockContext->getTemplate(), array(
            'nodes' => $tree,
            'menu' => $renderer->render($menu),

            'block' => $blockContext->getBlock(),
            'settings' => $blockContext->getSettings(),
        ), $response);
    }


    /**
     * @param $menu MenuItem
     * @param $nodesList
     */
    public function renderMenu($menu, $nodesList)
    {
        foreach ($nodesList as $item) {

            /** @var MenuItem $node */
            $node = $menu->addChild($item['name'], array('uri' => $item['url']));


            if ($item['url'] === $this->container->get('request')->getRequestUri()) {
                // URL's completely match
                $node->setCurrent(true);
            } else if ($item['url'] !== $this->container->get('request')->getBaseUrl() . '/' && (substr($this->container->get('request')->getRequestUri(), 0, strlen($item['url'])) === $item['url'])) {
                // URL isn't just "/" and the first container of the URL match
                $node->setCurrent(true);
            }


            if (count($item['__children'])) {
                $this->renderMenu($node, $item['__children']);
            }
        }
    }


    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $block->getEnabled();

        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('alias', 'text', array('required' => false)),
                array('class', 'text', array('required' => false)),

                array('template', 'text', array('required' => false)),
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'alias' => '',
            'class' => '',

            'template' => 'CompoMenuBundle:Menu:base_menu.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata('Меню', (!is_null($code) ? $code : $this->getName()), false, 'SonataBlockBundle', array(
            'class' => 'fa fa-file-text-o',

        ));
    }
}
