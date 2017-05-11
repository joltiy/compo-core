<?php

namespace Compo\ContactsBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\FormTypeExtensionInterface;

class ContactsAdmin extends AbstractAdmin
{


    /**
     * {@inheritDoc}
     */
    public function configure()
    {
       $this->setTranslationDomain('CompoContactsBundle');
       //$this->configureSeo(false);
       //$this->configureSettings(true, 'compo_articles');
       //$this->configureProperties(true);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('list')
            ->remove('delete');
    }


    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('address')
            ->add('worktime')
            ->add('phone')
            ->add('email')
            ->add('bankprops')
            ->add('walk_instruction')
            ->add('car_instruction')
            ->add('social_vk')
            ->add('social_fb')
            ->add('social_yt')
            ->add('social_tw')
            ->add('social_ig')
            ->add('social_gg')
            ->add('maps_code')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
           // ->add('address')
           // ->add('worktime')
            ->add('phone')
            ->add('email')
           // ->add('bankprops')
           // ->add('walk_instruction')
           // ->add('car_instruction')
           // ->add('social_vk')
           // ->add('social_fb')
           // ->add('social_yt')
           // ->add('social_tw')
           // ->add('social_ig')
           // ->add('social_gg')
          //  ->add('maps_code')
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                  //  'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('form.tab_main')
            ->with('form.group_major', array('class' => 'col-md-6'))
            ->add('worktime')
            ->add('phone')
            ->add('email')
            ->add('address','ckeditor')
            ->add('bankprops','ckeditor')
             ->end()->end()
            ->tab('form.tab_instructions')
            ->with('form.group_car', array('class' => 'col-md-6'))
            ->add('car_instruction','ckeditor')
            ->end()
            ->with('form.group_walk', array('class' => 'col-md-6'))
            ->add('walk_instruction','ckeditor')
            ->end()->end()
            ->tab('form.tab_social')
            ->with('form.group_links', array('class' => 'col-md-9'))
            ->add('social_vk')
            ->add('social_fb')
            ->add('social_yt')
            ->add('social_tw')
            ->add('social_ig')
            ->add('social_gg')
            ->add('maps_code')
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
            ->add('address')
            ->add('worktime')
            ->add('phone')
            ->add('email')
            ->add('bankprops')
            ->add('walk_instruction')
            ->add('car_instruction')
            ->add('social_vk')
            ->add('social_fb')
            ->add('social_yt')
            ->add('social_tw')
            ->add('social_ig')
            ->add('social_gg')
            ->add('maps_code')
        ;
    }
}
