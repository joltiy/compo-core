<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\PageBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class SharedBlockAdmin extends \Sonata\PageBundle\Admin\SharedBlockAdmin
{
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
