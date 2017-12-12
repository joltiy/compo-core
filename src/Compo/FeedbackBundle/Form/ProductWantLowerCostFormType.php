<?php

namespace Compo\FeedbackBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * {@inheritdoc}
 */
class ProductWantLowerCostFormType extends FeedbackBaseFormType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $productManager = $this->getContainer()->get('compo_product.manager.product');

        if (isset($options['extra_data'], $options['extra_data']['product'])) {
            $product = $options['extra_data']['product'];
        } else {
            $product = null;
        }

        if (isset($options['extra_data'], $options['extra_data']['product_id'])) {
            $product_id = $options['extra_data']['product_id'];
        } else {
            $product_id = null;
        }

        if (isset($options['extra_data'], $options['extra_data']['product_url'])) {
            $product_url = $options['extra_data']['product_url'];
        } else {
            $product_url = null;
        }

        $builder
            ->add('product', TextType::class, array(
                'attr' => array('readonly' => 'readonly'),
                'required' => false,
                'property_path' => 'data[product]',
                'data' => $product,
            ))
            ->add(
                'product_id',
                HiddenType::class,
                array(
                    'property_path' => 'data[product_id]',
                    'data' => $product_id,
                )
            )
            ->add(
                'product_url',
                HiddenType::class,
                array(
                    'property_path' => 'data[product_url]',
                    'data' => $product_url,
                )
            )

            ->add('quantity', IntegerType::class, array(
                'property_path' => 'data[quantity]',
            ))

            ->add('name', TextType::class)
            ->add('phone', TextType::class)
            ->add('message', TextareaType::class, array('required' => false));
    }
}
