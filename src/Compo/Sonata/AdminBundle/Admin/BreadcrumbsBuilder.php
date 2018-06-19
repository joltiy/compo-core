<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Admin;

use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\BreadcrumbsBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Хлебные крошки, с учётом родитетельской админки.
 */
final class BreadcrumbsBuilder implements BreadcrumbsBuilderInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param string[] $config
     */
    public function __construct(array $config = [])
    {
        $resolver = new OptionsResolver();

        $this->configureOptions($resolver);

        $this->config = $resolver->resolve($config);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'child_admin_route' => 'edit',
        ]);
    }

    /**
     * Get breadcrumbs for $action.
     *
     * @param AdminInterface $admin
     * @param string         $action the name of the action we want to get a
     *                               breadcrumbs for
     *
     * @return mixed array|Traversable the breadcrumbs
     */
    public function getBreadcrumbs(AdminInterface $admin, $action)
    {
        $breadcrumbs = [];

        if ($admin->isChild()) {
            return $this->getBreadcrumbs($admin->getParent(), $action);
        }

        $menu = $this->buildBreadcrumbs($admin, $action);

        /* @noinspection PhpAssignmentInConditionInspection */
        do {
            $breadcrumbs[] = $menu;
        } while ($menu = $menu->getParent());

        $breadcrumbs = array_reverse($breadcrumbs);

        array_shift($breadcrumbs);

        return $breadcrumbs;
    }

    /**
     * Builds breadcrumbs for $action, starting from $menu.
     *
     * Note: the method will be called by the top admin instance (parent => child)
     * NEXT_MAJOR : remove this method from the public interface.
     *
     * @param AdminInterface     $admin
     * @param string             $action
     * @param ItemInterface|null $menu
     *
     * @return ItemInterface
     */
    public function buildBreadcrumbs(AdminInterface $admin, $action, ItemInterface $menu = null)
    {
        /* @var AbstractAdmin $admin */
        if (!$menu) {
            $menu = $admin->getMenuFactory()->createItem('root');

            $menu = $menu->addChild(
                'link_breadcrumb_dashboard',
                [
                    'uri' => $admin->getRouteGenerator()->generate('sonata_admin_dashboard'),
                    'extras' => ['translation_domain' => 'SonataAdminBundle'],
                ]
            );
        }

        $childAdmin = $admin->getCurrentChildAdmin();

        $menu = $this->createMenuItem(
            $admin,
            $menu,
            'list',
            $admin->getTranslationDomain(),
            [
                'uri' => $admin->hasRoute('list') && $admin->hasAccess('list') ?
                    $admin->generateUrl('list') :
                    null,
                'translation_parameters' => [
                    '%name%' => $admin->getLabel(),
                ],
            ]
        );

        $menu->setExtra('translation_params', [
            '%name%' => $admin->getLabel(),
        ]);

        if ($childAdmin) {
            $id = $admin->getRequest()->get($admin->getIdParameter());

            $child_admin_route = $this->config['child_admin_route'];

            $menu = $menu->addChild(
                $admin->toString($admin->getSubject()),
                [
                    'uri' => $admin->hasRoute($child_admin_route) && $admin->hasAccess($child_admin_route, $admin->getSubject()) ?
                        $admin->generateUrl($child_admin_route, ['id' => $id]) :
                        null,
                    'extras' => [
                        'translation_domain' => false,
                    ],
                ]
            );

            $menu->setExtra('safe_label', false);

            return $this->buildBreadcrumbs($childAdmin, $action, $menu);
        }

        if ('list' === $action || 'tree' === $action) {
            $menu->setUri(false);
        } elseif ('create' !== $action && $admin->hasSubject()) {
            $menu = $menu->addChild($admin->toString($admin->getSubject()), [
                'extras' => [
                    'translation_domain' => false,
                ],
            ]);
        } else {
            $menu = $this->createMenuItem(
                $admin,
                $menu,
                sprintf('%s', $action),
                $admin->getTranslationDomain()
            );
        }

        return $menu;
    }

    /**
     * Creates a new menu item from a simple name. The name is normalized and
     * translated with the specified translation domain.
     *
     * @param AdminInterface $admin             used for translation
     * @param ItemInterface  $menu              will be modified and returned
     * @param string         $name              the source of the final label
     * @param string         $translationDomain for label translation
     * @param array          $options           menu item options
     *
     * @return ItemInterface
     */
    private function createMenuItem(
        AdminInterface $admin,
        ItemInterface $menu,
        $name,
        $translationDomain = null,
        array $options = []
    ) {
        $options = array_merge([
            'extras' => [
                'translation_domain' => $translationDomain,
            ],
        ], $options);

        return $menu->addChild(
            $admin->getLabelTranslatorStrategy()->getLabel(
                $name,
                'breadcrumb',
                'link'
            ),
            $options
        );
    }
}
