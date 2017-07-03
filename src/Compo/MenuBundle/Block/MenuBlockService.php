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

dump($settings);
        $menu = null;

        $tree = array();

        $factory = new MenuFactory();

        if ($settings['id']) {
            $menu = $factory->createItem($settings['id']);

            /** @var MenuRepository $repositoryMenu */
            $repositoryMenu = $em->getRepository("CompoMenuBundle:Menu");

            $menuObject = $repositoryMenu->find($settings['id']);

            /** @var MenuItemRepository $repo */
            $repo = $em->getRepository("CompoMenuBundle:MenuItem");

            $tree = $repo->childrenHierarchyWithNodes($repo->findOneBy(array('menu' => $menuObject)), false, array(), false);
        } else {
            $menu = $factory->createItem('');
        }

        $tree = $this->renderMenu($menu, $tree);

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

        foreach ($nodesList as $key => $item) {
            /** @var \Compo\MenuBundle\Entity\MenuItem $nodeItem */

            $nodeItem = $item['node'];

            if ($item['type'] == 'url') {

            } elseif ($item['type'] == 'page') {

                $item['url'] = $this->getContainer()->get('router')->generate('page_slug', array('path' => $nodeItem->getPage()->getUrl()));
            } elseif ($item['type'] == 'tagging') {

                if (!$nodeItem->getTagging()) {
                    continue;
                }

                $catalogManager = $this->getContainer()->get('compo_catalog.manager.catalog');

                $item['url'] = $catalogManager->getCatalogTaggingShowPermalink($nodeItem->getTagging()->getSlug());


                $criteria = array();
                $criteria['filter'] = $nodeItem->getTagging()->getFilterData();

                $filter = $catalogManager->getFilter($criteria);


                $item['products_count'] = $filter['products_count'];

            } else {

                $item['url'] = '';
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
            } else if ($item['url'] !== $this->getRequest()->getBaseUrl() . '/' && (substr($this->getRequest()->getRequestUri(), 0, strlen($item['url'])) === $item['url'])) {
                // URL isn't just "/" and the first container of the URL match
                $node->setCurrent(true);
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
        $block->getEnabled();

        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('id', 'choice', array('required' => true, 'choices' => $this->getMenuRepository()->getChoices())),

                array('class', 'text', array('required' => false)),
                array('template', 'text', array('required' => false)),

            ),
        ));
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
        $resolver->setDefaults(array(
            'class' => '',
            'id' => null,
            'template' => 'CompoMenuBundle:Menu:base_menu.html.twig',
        ));
    }

    public function getCacheKeys(BlockInterface $block)
    {
        $settings = $block->getSettings();

        if (isset($settings['id'])) {
            $em = $this->getContainer()->get("doctrine")->getManager();

            /** @var MenuRepository $repository */
            $repository = $em->getRepository("CompoMenuBundle:Menu");

            $item = $repository->find($settings['id']);

            $key = "CompoMenuBundle:Menu:" . $settings['id'];

            if (isset($settings['template'])) {
                $key = $key . ':' . $settings['template'];
            }

            return array(
                'block_id' => $key,
                'updated_at' => $item->getUpdatedAt()->format('U'),
            );
        } else {
            return parent::getCacheKeys($block);
        }

    }
}
