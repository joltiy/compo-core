<?php

namespace Compo\MenuBundle\Block;

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

        $tree = array();

        $factory = new MenuFactory();

        if ($settings['id']) {
            $menu = $factory->createItem($settings['id']);

            /** @var MenuRepository $repositoryMenu */
            $repositoryMenu = $em->getRepository('CompoMenuBundle:Menu');

            $menuObject = $repositoryMenu->find($settings['id']);

            /** @var MenuItemRepository $repo */
            $repo = $em->getRepository('CompoMenuBundle:MenuItem');

            $tree = $repo->childrenHierarchyWithNodes($repo->findOneBy(array('menu' => $menuObject)));
        } else {
            $menu = $factory->createItem('');
        }

        $tree = $this->renderMenu($menu, $tree);

        $renderer = new ListRenderer(new Matcher());

        return $this->renderResponse(
            $blockContext->getTemplate(),
            array(
                'nodes' => $tree,
                'menu' => $renderer->render($menu),

                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
            ),
            $response
        );
    }

    /**
     * @param $menu MenuItem
     * @param $nodesList
     *
     * @return array
     */
    public function renderMenu($menu, $nodesList)
    {
        foreach ($nodesList as $key => $item) {
            /** @var \Compo\MenuBundle\Entity\MenuItem $nodeItem */
            $nodeItem = $item['node'];

            if (!$nodeItem->getEnabled()) {
                unset($nodesList[$key]);
                continue;
            }

            if ('catalog' === $item['type']) {
                if (!$nodeItem->getCatalog()) {
                    continue;
                }

                $catalogManager = $this->getContainer()->get('compo_catalog.manager.catalog');

                $item['url'] = $catalogManager->getCatalogShowPermalink($nodeItem->getCatalog());
            } elseif ('page' === $item['type']) {
                $item['url'] = $this->getContainer()->get('router')->generate('page_slug', array('path' => $nodeItem->getPage()->getUrl()));
            } elseif ('tagging' === $item['type']) {
                if ($nodeItem->getTagging()) {
                    $catalogManager = $this->getContainer()->get('compo_catalog.manager.catalog');

                    $item['url'] = $catalogManager->getCatalogTaggingShowPermalink($nodeItem->getTagging()->getSlug());

                    $criteria = array();

                    $criteria['filter'] = $nodeItem->getTagging()->getFilterData();

                    $filter = $catalogManager->getFilter($criteria);

                    $item['tagging'] = $nodeItem->getTagging();

                    $item['products_count'] = $filter['products_count'];
                }
            } elseif ('manufacture' === $item['type']) {
                if ($nodeItem->getManufacture()) {
                    $manufactureManager = $this->getContainer()->get('compo_manufacture.manager.manufacture');

                    $item['url'] = $manufactureManager->getManufactureShowPermalink($nodeItem->getManufacture());

                    $item['manufacture'] = $nodeItem->getManufacture();
                }
            } elseif ('country' === $item['type']) {
                if ($nodeItem->getCountry()) {
                    $router = $this->getContainer()->get('router');

                    $item['url'] = $router->generate(
                        'catalog_index',
                        array(
                            'country' => array(
                                'items' => array(
                                    $nodeItem->getCountry()->getId() => $nodeItem->getCountry()->getId(),
                                ),
                            ),
                        )
                    );

                    $item['country'] = $nodeItem->getCountry();
                }
            } else {
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
            $node = $menu->addChild($item['id'], array('uri' => $item['url']));

            if ($item['url'] === $this->getRequest()->getRequestUri()) {
                // URL's completely match
                $node->setCurrent(true);
            } else {
                if ($item['url'] && $item['url'] !== $this->getRequest()->getBaseUrl() . '/' && (0 === strpos($this->getRequest()->getRequestUri(), $item['url']))) {
                    // URL isn't just "/" and the first container of the URL match
                    $node->setCurrent(true);
                }
            }

            if (count($item['__children'])) {
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
            array(
                'keys' => array(
                    array('id', 'choice', array('required' => true, 'choices' => $this->getMenuRepository()->getChoices())),
                    array('class', 'text', array('required' => false)),
                    array('template', 'text', array('required' => false)),
                ),
            )
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
     * @return \Doctrine\Bundle\DoctrineBundle\Registry|object
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
            array(
                'class' => '',
                'id' => null,
                'template' => 'CompoMenuBundle:Menu:base_menu.html.twig',
            )
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
