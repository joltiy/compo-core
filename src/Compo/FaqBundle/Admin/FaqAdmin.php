<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\FaqBundle\Admin;

use Compo\FaqBundle\Entity\Faq;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Knp\Menu\MenuFactory;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class FaqAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setSortBy('publicationAt');
        $this->setSortOrder('DESC');
    }

    /**
     * @param $object Faq
     *
     * @return string
     */
    public function generatePermalink($object = null)
    {
        $manager = $this->getContainer()->get('compo_faq.manager.faq');

        if (null === $object) {
            return $manager->getFaqIndexPermalink();
        }

        return $manager->getArticleShowPermalink($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('description')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('publicationAt');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('publicationAt')
            ->addIdentifier('name')
            ->add('enabled')
            ->add(
                '_action'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->tab('main');
        $formMapper->with('main', ['name' => false]);

        $formMapper
            ->add('id')
            ->add('enabled')
            ->add('publicationAt')
            ->add('views')
            ->add('username')
            ->add('email')
            ->add('name')
            ->add('description', CKEditorType::class, ['attr' => ['class' => ''], 'required' => false])
            ->add('answer', CKEditorType::class, ['attr' => ['class' => ''], 'required' => false])
        ;

        $formMapper->end();
        $formMapper->end();

        $formMapper->tab('media');
        $formMapper->with('media_image');

        $formMapper->add('image');

        $formMapper->end();
        $formMapper->end();
    }

    /**
     * @param $context
     * @param $vars
     *
     * @return \Knp\Menu\ItemInterface|\Knp\Menu\MenuItem
     */
    public function configureAdminNavBar($context, $vars)
    {
        $factory = new MenuFactory();

        $menu = $factory->createItem($context);

        $tabMenuDropdown = $menu->addChild(
            'tab_menu.list_mode.' . $this->getLabel(),
            [
                'label' => $this->getContainer()->get('translator')->trans($this->getLabel(), [], $this->getTranslationDomain()),
                'attributes' => ['dropdown' => true],
            ]
        );

        $menu->setAttribute('icon', 'fa fa-list')->setAttribute('is_dropdown', true)->setAttribute('is_dropdown', true);
        $tabMenuDropdown->setChildrenAttribute('class', 'dropdown-menu');

        $tabMenuDropdown->addChild('list', [
            'uri' => $this->generateUrl('list', []),
            'label' => 'Список',
        ])->setAttribute('icon', 'fa fa-list');

        if ('faq_show' === $context) {
            /** @var Faq $faq */
            $faq = $vars['faq'];

            $tabMenuDropdown->addChild('edit', [
                'uri' => $this->generateUrl('edit', ['id' => $faq->getId()]),
                'label' => 'Редактировать',
            ])->setAttribute('icon', 'fa fa-pencil');
        }

        return $menu;
    }
}
