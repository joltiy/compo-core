<?php

namespace Compo\MenuBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class MenuAdmin extends Admin
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('CompoMenuBundle');

        $this->configureTree(true);
    }


    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('enabled')
            ->add('title')
            ->add('url')
            ->add('position')
            ->add('createdAt')
            ->add('updatedAt');
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
            ->add('enabled', null, array(
                'editable' => true,
                'required' => true
            ))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();

        if (is_null($subject)) {
            return;
        }

        $id = $subject->getId();

        $queryBuilder = $this->getDoctrine()->getManager()->getRepository('CompoMenuBundle:Menu')->createQueryBuilder('c')
            ->select('c')
            ->from($this->getClass(), 'c')
            ->orderBy('c.root, c.lft', 'ASC');

        if ($id) {
            $queryBuilder->where('c.id <> :id')
                ->setParameter('id', $id);
        }

        $tree = $queryBuilder->getQuery()->getResult();


        $formMapper
            ->tab('form.tab_main')
            ->with('main_tab', array('name' => false))
            ->add('enabled')
            ->add('name')
            ->add('title')
            ->add('url')
            ->add('alias')
            ->add('parent', 'compo_tree_selector', array(
                'model_manager' => $this->getModelManager(),
                'class' => $this->getClass(),
                'tree' => $tree,
                'required' => false,
            ));


        $formMapper
            ->end()
            ->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name');
    }
}
