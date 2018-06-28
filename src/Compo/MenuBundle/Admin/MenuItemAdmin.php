<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\MenuBundle\Admin;

use Compo\MenuBundle\Entity\MenuItemRepository;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Form\Type\TreeSelectorType;
use Doctrine\DBAL\Query\QueryBuilder;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * {@inheritdoc}
 */
class MenuItemAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('name')
            ->add('url')
            ->add('enabled')
            ->add(
                '_action',
                null,
                [
                ]
            );
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();

        if (null === $subject) {
            $id = null;
            $menu_id = null;
            $root_menu_item = null;
        } else {
            $id = $subject->getId();

            $admin = $this->isChild() ? $this->getParent() : $this;

            $menu_id = $admin->getRequest()->get('id');
            $root_menu_item = $this->getDoctrine()->getRepository('CompoMenuBundle:MenuItem')->findOneBy(['menu' => $menu_id]);
        }

        /** @var MenuItemRepository $repository */
        $repository = $this->getRepository();

        if ($root_menu_item) {
            // Родительские категории
            $tree = $repository->getForTreeSelector(
                $id,
                function ($qb) use ($root_menu_item) {
                    /* @var QueryBuilder $qb */
                    $qb->andWhere('c.root = ' . $root_menu_item->getId());
                }
            );
        } else {
            // Родительские категории
            $tree = $repository->getForTreeSelector($id);
        }

        $formMapper->tab(
            'main_menu',
            [
                'translation_domain' => $this->getTranslationDomain(),
            ]
        );

        $formMapper->with(
            'main',
            [
                'name' => false,
            ]
        );

        $formMapper
            ->add('id')
            ->add('enabled')
            ->add('name')
            ->add('title');

        $formMapper->add(
            'parent',
            TreeSelectorType::class,
            [
                'current' => $subject,
                'model_manager' => $this->getModelManager(),
                'class' => $this->getClass(),
                'tree' => $tree,
                'required' => true,
            ]
        );

        $menuManager = $this->getContainer()->get('compo_menu.manager');

        $choices = $menuManager->getMenuTypeChoices();

        $choices = array_merge(['menu_type.url' => 'url'], $choices);

        $formMapper
            ->add(
                'type',
                'sonata_type_choice_field_mask',
                [
                    'choices' => $choices,
                    'choice_translation_domain' => 'CompoMenuBundle',
                    'required' => true,
                    'attr' => ['class' => 'menu-type-select'],
                ]
            );

        $choicesTarget = [];

        if ($this->getSubject() && !$this->isCurrentRoute('create')) {
            $type = $this->getSubject()->getType();

            if ('url' !== $type) {
                $menuType = $menuManager->getMenuType($type);

                $choicesTarget = $menuType->getChoices();
            }
        }

        $formMapper->add('targetId', ChoiceType::class, [
            'choices' => $choicesTarget,
            'required' => false,
            'attr' => ['class' => 'menu-target-id'],
        ]);

        $formMapper->add('url');

        $formMapper->add(
            'target',
            'choice',
            [
                'required' => false,
                'choices' => [
                    'В новом окне' => '_blank',
                ],
                'multiple' => false,
            ]
        );

        $formMapper->add('image');

        $formMapper->end();
        $formMapper->end();
    }

    /**
     * @return mixed
     */
    public function getTreeNodes()
    {
        $request = $this->getRequest();

        $em = $this->getDoctrine()->getManager();

        /** @var NestedTreeRepository $repo */
        $repo = $em->getRepository($this->getClass());
        $repo->verify();
        $repo->recover();
        $em->flush();

        $node = $repo->findOneBy(['menu' => $request->get('id')]);

        /* @noinspection PhpUndefinedMethodInspection */
        return $repo->childrenHierarchyWithNodes($node);
    }
}
