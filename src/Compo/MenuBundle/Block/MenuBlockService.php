<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\MenuBundle\Block;

use Compo\MenuBundle\Entity\Menu;
use Compo\MenuBundle\Entity\MenuItemRepository;
use Compo\MenuBundle\Entity\MenuRepository;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Knp\Menu\Renderer\ListRenderer;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class MenuBlockService extends AbstractBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $settings = $blockContext->getSettings();

        $menu = null;

        $tree = [];

        $factory = new MenuFactory();

        if ($settings['id']) {
            $menu = $factory->createItem($settings['id']);

            /** @var MenuRepository $repositoryMenu */
            $repositoryMenu = $em->getRepository(Menu::class);

            $menuObject = $repositoryMenu->find($settings['id']);

            /** @var MenuItemRepository $repo */
            $repo = $em->getRepository(\Compo\MenuBundle\Entity\MenuItem::class);

            $tree = $repo->childrenHierarchyWithNodes($repo->findOneBy(['menu' => $menuObject]));
        } else {
            $menu = $factory->createItem('');
        }

        $tree = $this->renderMenu($menu, $tree);

        $renderer = new ListRenderer(new Matcher());

        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'nodes' => $tree,
                'menu' => $renderer->render($menu),

                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
            ],
            $response
        );
    }

    /**
     * @param $menu MenuItem
     * @param array $nodesList
     *
     * @return array
     */
    public function renderMenu($menu, $nodesList)
    {
        $menuManger = $this->getContainer()->get('compo_menu.manager');

        foreach ($nodesList as $key => $item) {
            /** @var \Compo\MenuBundle\Entity\MenuItem $nodeItem */
            $nodeItem = $item['node'];

            if (!$nodeItem->getEnabled()) {
                unset($nodesList[$key]);
                continue;
            }

            $targetId = $nodeItem->getTargetId();
            $type = $nodeItem->getType();

            if ('url' !== $type && $targetId) {
                $menuType = $menuManger->getMenuType($type);

                $menuType->fillMenuItem($item);
            } else {
                $nodeItem->setType('url');
                $item['url'] = $nodeItem->getUrl();
            }

            $item['node'] = $nodeItem;

            $image = $nodeItem->getImage();

            if ($image) {
                $item['image'] = $image;
            } else {
                $item['image'] = null;
            }

            /** @var MenuItem $node */
            $node = $menu->addChild($item['id'], ['uri' => $item['url']]);

            if ($item['url'] === $this->getRequest()->getRequestUri()) {
                // URL's completely match
                $node->setCurrent(true);
            } else if ($item['url'] && $item['url'] !== $this->getRequest()->getBaseUrl() . '/' && (0 === mb_strpos($this->getRequest()->getRequestUri(), $item['url']))) {
                // URL isn't just "/" and the first container of the URL match
                $node->setCurrent(true);
            }

            if (\count($item['__children'])) {
                $item['__children'] = $this->renderMenu($node, $item['__children']);
            }

            $nodesList[$key] = $item;
        }

        return $nodesList;
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
                    ['id', 'choice', ['required' => true, 'choices' => $this->getMenuRepository()->getChoices()]],
                    ['class', 'text', ['required' => false]],
                    ['template', 'text', ['required' => false]],
                ],
            ]
        );
    }

    /**
     * @return MenuRepository
     */
    public function getMenuRepository()
    {
        return $this->getDoctrine()->getRepository('CompoMenuBundle:Menu');
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'class' => '',
                'id' => null,
                'template' => 'CompoMenuBundle:Menu:base_menu.html.twig',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys(BlockInterface $block)
    {
        $settings = $block->getSettings();

        $keys = parent::getCacheKeys($block);

        $keys['environment'] = $this->getContainer()->get('kernel')->getEnvironment();

        if (isset($settings['id'])) {
            $em = $this->getContainer()->get('doctrine')->getManager();

            /** @var MenuRepository $repository */
            $repository = $em->getRepository('CompoMenuBundle:Menu');

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
