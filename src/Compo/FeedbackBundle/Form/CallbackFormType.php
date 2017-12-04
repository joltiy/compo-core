<?php

namespace Compo\FeedbackBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * {@inheritdoc}
 */
class CallbackFormType extends FeedbackBaseFormType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('name', TextType::class)
            ->add('phone', TextType::class)
            ->add('message', TextareaType::class, array('required' => false));
    }
}
