<?php

namespace Compo\Sonata\AdminBundle\Form\Type;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritdoc}
 */
class HelpType extends AbstractType
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'mapped' => false,
                'template' => '',
                'required' => false,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                function ($value) use ($options) {
                    if (isset($options['template']) && $options['template']) {
                        if (0 === mb_strpos($options['template'], 'Compo')) {
                            return $this->getContainer()->get('twig')->render($options['template'], ['value' => $value]);
                        } else {
                            return $options['template'];
                        }
                    } else {
                        return $value;
                    }
                },
                function ($value) use ($options) {
                    return '';
                }
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'compo_admin_type_help';
    }
}
