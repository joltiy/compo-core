<?php

/**
 * Создание snapshots, после редактирование
 */

namespace Compo\Sonata\PageBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\PageBundle\Entity\BaseBlock;


/**
 * {@inheritDoc}
 */
class BlockAdmin extends \Sonata\PageBundle\Admin\BlockAdmin
{
    /**
     * {@inheritDoc}
     */
    public function postPersist($object)
    {
        parent::postPersist($object);

        $container = $this->getConfigurationPool()->getContainer();

        $container->get('sonata.notification.backend.runtime')->createAndPublish(
            'sonata.page.create_snapshot',
            array(
                'pageId' => $object->getPage()->getId()
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function preRemove($object)
    {
        parent::preRemove($object);

        $container = $this->getConfigurationPool()->getContainer();

        $container->get('sonata.notification.backend.runtime')->createAndPublish(
            'sonata.page.create_snapshot',
            array(
                'pageId' => $object->getPage()->getId()
            )
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param BaseBlock $object
     */
    public function postRemove($object)
    {
        parent::postRemove($object);

        $container = $this->getConfigurationPool()->getContainer();

        $container->get('sonata.notification.backend.runtime')->createAndPublish(
            'sonata.page.create_snapshot',
            array(
                'pageId' => $object->getPage()->getId()
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function postUpdate($object)
    {
        parent::postUpdate($object);

        $container = $this->getConfigurationPool()->getContainer();

        $container->get('sonata.notification.backend.runtime')->createAndPublish(
            'sonata.page.create_snapshot',
            array(
                'pageId' => $object->getPage()->getId()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $block = $this->getSubject();

        if (null === $block) {
            return;
        }

        parent::configureFormFields($formMapper);
    }
}
