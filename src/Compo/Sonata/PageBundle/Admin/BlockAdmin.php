<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\PageBundle\Admin;

/**
 * {@inheritdoc}
 */
class BlockAdmin extends \Sonata\PageBundle\Admin\BlockAdmin
{

    /**
     * {@inheritdoc}
     */
    public function postUpdate($object)
    {
        parent::postUpdate($object);

        $container = $this->getConfigurationPool()->getContainer();

        $container->get('sonata.notification.backend.runtime')->createAndPublish(
            'sonata.page.create_snapshot',
            [
                'pageId' => $object->getPage()->getId(),
            ]
        );
    }


}
