<?php

namespace Compo\FeedbackBundle\Form;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritDoc}
 */
class FeedbackBaseFormType extends AbstractType
{
    use ContainerAwareTrait;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'type',
                HiddenType::class,
                array(
                    'data' => $options['type']
                )
            )
            ->add(
                'page',
                HiddenType::class,
                array(
                    'data' => $this->getRequest()->getUri()
                )
            );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->getContainer()->get('request_stack')->getCurrentRequest();
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'extra_data' => array(),

                'type' => '',
                'data_class' => 'Compo\FeedbackBundle\Entity\Feedback',
                'translation_domain' => 'CompoFeedbackBundle',
            )
        );
    }
}
