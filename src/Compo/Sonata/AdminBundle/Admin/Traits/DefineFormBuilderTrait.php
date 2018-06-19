<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Admin\Traits;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Заменяет FormMapper на \Compo\Sonata\AdminBundle\Form\FormMapper.
 */
trait DefineFormBuilderTrait
{
    /**
     * This method is being called by the main admin class and the child class,
     * the getFormBuilder is only call by the main admin class.
     *
     * @param FormBuilderInterface $formBuilder
     */
    public function defineFormBuilder(FormBuilderInterface $formBuilder)
    {
        /** @var AbstractAdmin $admin */
        $admin = $this;

        $mapper = new \Compo\Sonata\AdminBundle\Form\FormMapper($admin->getFormContractor(), $formBuilder, $admin);

        $this->configureFormFields($mapper);

        foreach ($admin->getExtensions() as $extension) {
            $extension->configureFormFields($mapper);
        }

        $this->attachInlineValidator();
    }

    /**
     * Attach the inline validator to the model metadata, this must be done once per admin.
     */
    abstract protected function attachInlineValidator();

    /**
     * @param FormMapper $form
     *
     * @return mixed
     */
    abstract protected function configureFormFields(FormMapper $form);
}
