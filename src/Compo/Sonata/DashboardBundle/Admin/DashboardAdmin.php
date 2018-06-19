<?php

declare(strict_types=1);

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\DashboardBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\DashboardBundle\Entity\Block;
use Compo\Sonata\DashboardBundle\Entity\Dashboard;
use Compo\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Admin definition for the Dashboard class.
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
class DashboardAdmin extends AbstractAdmin
{
    /**
     * @return array
     */
    protected function getAccess()
    {
        return array_merge([
            'compose' => 'EDIT',
            'composeContainerShow' => 'LIST',
        ], parent::getAccess());
    }

    /**
     * @param string $context
     *
     * @return \Doctrine\ORM\QueryBuilder|\Sonata\AdminBundle\Datagrid\ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        /** @var User $user */
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return $query;
        }

        $query->leftJoin($query->getRootAliases()[0] . '.userGroups', 'ug');

        $query->andWhere('ug.id = :group_id');
        $query->setParameter('group_id', $user->getGroups());

        return $query;
    }

    /**
     * @param MenuItemInterface   $tabMenu
     * @param                     $action
     * @param AdminInterface|null $childAdmin
     */
    protected function configureTabMenu(MenuItemInterface $tabMenu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($tabMenu, $action, $childAdmin);

        $tabMenu->removeChild('tab_menu.link_list_block');

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        if (\in_array($action, ['delete', 'edit', 'history', 'untrash'], true)) {
            $tabMenu->addChild(
                $admin->trans('tab_menu.link_render'),
                ['uri' => $admin->generateUrl('render', ['id' => $admin->getSubject()->getId()])]
            )->setAttribute('icon', 'fa fa-eye');

            $tabMenu->addChild('sidemenu.link_compose_dashboard',
                ['uri' => $admin->generateUrl('compose', ['id' => $id])]
            )->setAttribute('icon', 'fa fa-folder');
        }

        if ('render' === $action) {
            $tabMenuDropdown = $tabMenu->addChild(
                'tab_menu.list_mode.' . $admin->getLabel(),
                [
                    'label' => $admin->getSubject()->getName(),
                    'uri' => $admin->generateUrl('render', ['id' => $id]),
                    'attributes' => ['dropdown' => true],
                ]
            )->setAttribute('icon', 'fa fa-list');

            $qb = $this->createQuery('list');

            /** @var array $list */
            $list = $qb->getQuery()->getResult();

            /** @var Block $listItem */
            foreach ($list as $listItem) {
                $tabMenuDropdown->addChild(
                    'tab_menu.list_mode.list.' . $admin->getLabel() . $listItem->getId(),
                    [
                        'label' => $listItem->getName(),
                        'uri' => $admin->generateUrl('render', ['id' => $listItem->getId()]),
                    ]
                );
            }
        }
    }

    /**
     * @param      $action
     * @param null $object
     *
     * @return array
     */
    public function configureActionButtons($action, $object = null)
    {
        $list = parent::configureActionButtons($action, $object);

        if (\in_array($action, ['show', 'delete', 'acl', 'history', 'render'], true)
            && $this->canAccessObject('edit', $object)
            && $this->hasRoute('edit')
        ) {
            $list['edit'] = [
                'template' => $this->getTemplateRegistry()->getTemplate('button_edit'),
            ];
        }

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function configureRoutes(RouteCollection $collection): void
    {
        parent::configureRoutes($collection);

        $collection->add('compose', '{id}/compose', [
            'id' => null,
        ]);

        $collection->add('render', '{id}/render', [
            'id' => null,
        ]);

        $collection->add('compose_container_show', 'compose/container/{id}', [
            'id' => null,
        ]);

        $collection->add('render_block', 'render/block/{id}', [
            'id' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object): void
    {
        /* @var Dashboard $object */
        $object->setEdited(true);
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object): void
    {
        /* @var Dashboard $object */
        $object->setEdited(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('name')
            ->add('enabled', null, ['editable' => true])
            ->add('allowEdit', null, ['editable' => true])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('allowEdit')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        // define group zoning
        $formMapper
            ->with('form_dashboard.group_main_label', ['class' => 'col-md-12'])->end()
        ;

        $formMapper
            ->with('form_dashboard.group_main_label')
                ->add('name')
                ->add('enabled', CheckboxType::class, ['required' => false])
                ->add('userGroups','sonata_type_model_autocomplete',
                    [
                        'required' => false,

                        'multiple' => true,
                        'property' => 'name',
                        'minimum_input_length' => 0,
                        'cache' => true,
                        'items_per_page' => 0,
                        'callback' => function ($admin, $property, $value) {
                            /** @var AbstractAdmin $admin */
                            $datagrid = $admin->getDatagrid();

                            /** @var QueryBuilder $queryBuilder */
                            $queryBuilder = $datagrid->getQuery();

                            $queryBuilder->orderBy($queryBuilder->getRootAliases()[0] . '.name', 'ASC');
                            $datagrid->setValue($property, null, $value);
                        },
                    ])
            ->add('allowEdit', CheckboxType::class, ['required' => false])

            ->end()
        ;

        $formMapper->setHelps([
            'name' => 'help_dashboard_name',
        ]);
    }
}
