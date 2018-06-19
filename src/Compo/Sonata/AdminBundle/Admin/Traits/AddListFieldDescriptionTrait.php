<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Admin\Traits;

use Sonata\AdminBundle\Admin\FieldDescriptionInterface;

/**
 * Замена шаблона в списке элементов, для столбцов с типом orm_many_to_many/orm_many_to_one для быстрого редактирования.
 */
trait AddListFieldDescriptionTrait
{
    /**
     * addListFieldDescription.
     *
     * @param string                    $name
     * @param FieldDescriptionInterface $fieldDescription
     */
    public function addListFieldDescription($name, FieldDescriptionInterface $fieldDescription)
    {
        if ($fieldDescription->getOption('editable', false)) {
            $type = $fieldDescription->getType();

            if (
                'orm_many_to_many' === $type
            ) {
                $fieldDescription->setTemplate('@CompoSonataAdmin/SonataDoctrineORMAdminBundle/CRUD/list_orm_many_to_many.html.twig');
            }

            if (
                'orm_many_to_one' === $type
            ) {
                $fieldDescription->setTemplate('@CompoSonataAdmin/SonataDoctrineORMAdminBundle/CRUD/list_orm_many_to_one.html.twig');
            }
        }

        parent::addListFieldDescription($name, $fieldDescription);
    }
}
