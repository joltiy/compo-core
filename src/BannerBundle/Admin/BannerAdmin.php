<?php

namespace Compo\BannerBundle\Admin;

use Compo\BannerBundle\Entity\BannerItem;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * {@inheritDoc}
 */
class BannerAdmin extends AbstractAdmin
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('CompoBannerBundle');

        $this->configureProperties(true);
    }


    /**
     * {@inheritDoc}
     */
    public function postRemove($object)
    {
        $banner_items = $this->getDoctrine()->getRepository('CompoBannerBundle:BannerItem')->findBy(array('banner' => $object));

        /** @var BannerItem $item */
        foreach ($banner_items as $item) {
            $item->setDeletedAt(new \DateTime());

            $this->getDoctrine()->getManager()->persist($item);
        }

        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('alias')
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
            ->add('alias')
            ->add('description')
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
        $formMapper
            ->tab('form.tab_main')
            ->with('form.group_main', array('name' => false))
            ->add('id')
            ->add('name')
            ->add('alias', null, array('required' => false))
            ->add('description');

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

    /**
     * {@inheritDoc}
     */
    protected function configureTabMenu(MenuItemInterface $banner, $action, AdminInterface $childAdmin = null)
    {

        if (!$childAdmin && in_array($action, array('edit'))) {
            $this->configureTabBannerList($banner, $action);
        }

        if ($childAdmin && in_array($action, array('list'))) {
            $this->configureTabBannerList($banner, $action);
        }

        /** @var BannerItemAdmin $childAdmin */
        if ($childAdmin && in_array($action, array('edit'))) {
            $childAdmin->configureTabBannerItem($banner, $action);

            $tabBanner = $banner->addChild('tab_menu.banner',
                array(
                    'label' => $this->trans('tab_menu.banner', array('%name%' => $this->getSubject()->getName())),
                    'attributes' => array('dropdown' => true)
                )
            );

            $this->configureTabBannerList($tabBanner, $action);
        }
    }

    /**
     * @param MenuItemInterface $banner
     * @param $action
     * @param AdminInterface|null $childAdmin
     */
    public function configureTabBannerList(MenuItemInterface $banner, $action, AdminInterface $childAdmin = null)
    {
        if ($childAdmin) {
            $subject = $childAdmin->getSubject()->getBanner();
        } else {
            $subject = $this->getSubject();
        }

        $banner->addChild(
            $this->trans('tab_menu.link_edit'),
            array('uri' => $this->generateUrl('edit', array('id' => $subject->getId())))
        );

        $banner->addChild(
            $this->trans('tab_menu.link_banner_list'),
            array('uri' => $this->generateUrl('compo_banner.admin.banner|compo_banner.admin.banner_item.list', array('id' => $subject->getId())))
        );
    }
}
