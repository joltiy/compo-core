<?php

namespace Compo\Sonata\AdminBundle\Form\Type;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * {@inheritDoc}
 */
class HelpType extends AbstractType
{
    use ContainerAwareTrait;


    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'mapped' => false,
            'template' => '',
            'required' => false
        ));
    }


    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addModelTransformer(new CallbackTransformer(
            function ($value) use ($options) {

                if (isset($options['template']) && $options['template']) {
                    $value = $options['template'];
                }

                if (strpos($value, 'Compo') === 0) {
                    return $this->getContainer()->get('twig')->render($options['template']);

                } else {
                    return $options['template'];
                }

            },
            function ($value) use ($options) {
                return '';
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'compo_admin_type_help';
    }
}