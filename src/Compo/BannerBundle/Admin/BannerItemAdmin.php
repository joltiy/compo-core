<?php

namespace Compo\BannerBundle\Admin;

use Compo\BannerBundle\Entity\BannerItem;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritDoc}
 */
class BannerItemAdmin extends AbstractAdmin
{
    /**
     * Конфигурация админки
     */
    public function configure()
    {
        $this->setTranslationDomain('CompoBannerBundle');
        $this->configurePosition(true, array('banner'));
        $this->setParentParentAssociationMapping('banner');
        $this->configureProperties(true);
    }

    /**
     * {@inheritDoc}
     */
    public function preUpdate($object)
    {
        $this->updateParent($object);
    }

    /**
     * @param $object BannerItem
     */
    public function updateParent($object)
    {
        if ($object->getBanner()) {
            $object->getBanner()->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function prePersist($object)
    {
        $this->updateParent($object);
    }

    /**
     * {@inheritDoc}
     */
    public function preRemove($object)
    {
        $this->updateParent($object);
    }

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
            ->add('_action', null, array(
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
        $formMapper->tab('form.tab_main_banner', array(
            'translation_domain' => $this->getTranslationDomain()
        ));

        $formMapper->with('form.tab_main', array(
            'name' => false
        ))
            ->add('id')
            ->add('enabled')
            ->add('banner')
            ->add('name')
            ->add('title');


        $formMapper->add('url');

        $formMapper->add('image');

        $formMapper->end();
        $formMapper->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('url')
            ->add('name')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt');
    }

    /**
     * {@inheritDoc}
     */
    protected function configureTabMenu(MenuItemInterface $banner, $action, AdminInterface $childAdmin = null)
    {
        if (in_array($action, array('edit'))) {
            $this->configureTabBannerItem($banner, $action);

            /** @var BannerAdmin $bannerAdmin */
            $bannerAdmin = $this->getConfigurationPool()->getAdminByAdminCode('compo_banner.admin.banner');
            $bannerAdmin->setSubject($this->getSubject()->getBanner());
            $tabBanner = $banner->addChild('tab_menu.banner', array('label' => $this->trans('tab_menu.banner', array('%name%' => $this->getSubject()->getBanner()->getName())), 'attributes' => array('dropdown' => true)));

            $bannerAdmin->configureTabBannerList($tabBanner, $action);
        }
    }

    /**
     * @param MenuItemInterface $banner
     * @param $action
     * @param AdminInterface|null $childAdmin
     */
    public function configureTabBannerItem(MenuItemInterface $banner, $action, AdminInterface $childAdmin = null)
    {
        $banner->addChild(
            $this->trans('tab_menu.link_edit'),
            array('uri' => $this->generateUrl('edit', array('id' => $this->getSubject()->getId())))
        );
    }
}
