<?php

namespace Compo\SeoBundle\Admin;

use Compo\SeoBundle\Entity\SeoPage;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Form\Type\HelpType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritdoc}
 */
class SeoPageAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setSortBy('context');
        $this->setSortOrder('ASC');
    }

    /**
     * @param mixed $object
     */
    public function prePersist($object)
    {
        $this->fixData($object);
    }

    /**
     * @param $object SeoPage
     */
    public function fixData($object)
    {
        $manager = $this->getContainer()->get('compo_seo.page.manager');

        $context = $manager->getSeoPageItem($object->getContext());

        if (!$object->getHeader()) {
            $object->setHeader($context['header']);
        }
        if (!$object->getDescription()) {
            $object->setDescription($context['description']);
        }

        if (!$object->getDescriptionAdditional()) {
            $object->setDescriptionAdditional($context['descriptionAdditional']);
        }

        if (!$object->getTitle()) {
            $object->setTitle($context['title']);
        }

        if (!$object->getMetaDescription()) {
            $object->setMetaDescription($context['metaDescription']);
        }

        if (!$object->getMetaKeyword()) {
            $object->setMetaKeyword($context['metaKeyword']);
        }
    }

    /**
     * @param mixed $object
     */
    public function preUpdate($object)
    {
        $this->fixData($object);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('context')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier(
                'context',
                'trans',
                array(
                    'catalogue' => 'CompoSeoBundle',
                )
            )
            ->add('header')
            ->add('title')
            ->add('metaDescription')
            ->add('metaKeyword')
            ->add(
                '_action',
                null,
                array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                    ),
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $manager = $this->getContainer()->get('compo_seo.page.manager');

        $subject = $this->getSubject();

        if ($this->isCurrentRoute('create')) {
            $help = '';
        } else {
            $context = $manager->getSeoPageItem($subject->getContext());

            $help = 'CompoSeoBundle:Form:seo_vars.html.twig';

            if (isset($context['help'])) {
                $help = $context['help'];
            }
        }

        $formMapper
            ->tab('main')
            ->with('main', array('name' => false, 'class' => 'col-lg-12'));

        $formMapper->add('id')
            ->add(
                'context',
                'choice',
                array(
                    'choices' => $manager->getChoices(),
                    'choice_translation_domain' => 'CompoSeoBundle',
                )
            )
            ->add('title', null, array('attr' => array('class' => 'highlight-src'), 'required' => false))
            ->add('metaKeyword', null, array('attr' => array('class' => 'highlight-src'), 'required' => false))
            ->add('metaDescription', null, array('attr' => array('class' => 'highlight-src'), 'required' => false))
            ->add('header', null, array('attr' => array('class' => 'highlight-src'), 'required' => false))
            ->add('description', null, array('attr' => array('class' => ''), 'required' => false))
            ->add('descriptionAdditional', CKEditorType::class, array('attr' => array('class' => ''), 'required' => false));

        $formMapper->add(
            'help',
            HelpType::class,
            array(
                'template' => $help,
            )
        );

        $formMapper->end()
            ->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('context')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt');
    }
}
