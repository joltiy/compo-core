<?php

namespace Compo\ContactsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
class FeedbackFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name',TextType::class )
            ->add('email',EmailType::class )
            ->add('phone',TextType::class )
            ->add('message',TextareaType::class )
            ->add('page',HiddenType::class );
        ;
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Compo\ContactsBundle\Entity\Feedback',
            'translation_domain' => 'CompoContactsBundle',
            'csrf_token_id'   => 'feedback_protection'
        ));
    }


}
