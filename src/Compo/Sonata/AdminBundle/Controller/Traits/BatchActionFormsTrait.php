<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Controller\Traits;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Создание формы для пакетного действия.
 */
trait BatchActionFormsTrait
{
    /**
     * @param string $name
     *
     * @return FormBuilderInterface
     */
    public function createBatchActionForm($name)
    {
        /** @var AbstractAdmin $admin */
        $admin = $this->getAdmin();

        $container = $admin->getContainer();

        return $container->get('form.factory')->createNamedBuilder($name, 'form', [], [
            'label_format' => 'form.label_%name%',
            'translation_domain' => $admin->getTranslationDomain(),
        ]);
    }

    /**
     * @return array
     */
    public function getBatchActionFormViews()
    {
        $actionForms = [];

        foreach ($this->configureBatchActionForms() as $formName => $form) {
            /* @var FormBuilderInterface $form */
            $actionForms[$formName] = $form->getForm()->createView();
        }

        return $actionForms;
    }

    /**
     * @return array
     */
    public function configureBatchActionForms()
    {
        return [];
    }
}
