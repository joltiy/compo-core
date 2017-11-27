<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Form\Type;

use Sonata\AdminBundle\Form\ChoiceList\ModelChoiceList;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Select a category.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class TreeSelectorType extends ModelType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $options = array();
        $propertyAccessor = $this->propertyAccessor;
        if (interface_exists('Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface')) { // SF2.7+
            $options['choice_loader'] = function (Options $options, $previousValue) use ($propertyAccessor) {


                return new \Compo\Sonata\AdminBundle\Form\ModelChoiceLoader(
                    $options['model_manager'],
                    $options['class'],
                    $options['property'],
                    $options['query'],
                    $options['choices'],
                    $propertyAccessor,
                    $options
                );
            };
            // NEXT_MAJOR: Remove this when dropping support for SF 2.8
            if (method_exists('Symfony\Component\Form\FormTypeInterface', 'setDefaultOptions')) {
                $options['choices_as_values'] = true;
            }
        } else {
            $options['choice_list'] = function (Options $options, $previousValue) use ($propertyAccessor) {
                
                if ($previousValue && count($choices = $previousValue->getChoices())) {
                    return $choices;
                }

                return new ModelChoiceList(
                    $options['model_manager'],
                    $options['class'],
                    $options['property'],
                    $options['query'],
                    $options['choices'],
                    $propertyAccessor
                );
            };
        }

        $options['choice_list'] = function (Options $options, $previousValue) use ($propertyAccessor) {
            return $this->getChoices($options);
        };

        $resolver->setDefaults(
            array_merge(
                $options,
                array(
                    'compound' => function (Options $options) {
                        if (isset($options['multiple']) && $options['multiple']) {
                            if (isset($options['expanded']) && $options['expanded']) {
                                //checkboxes
                                return true;
                            }

                            //select tag (with multiple attribute)
                            return false;
                        }

                        if (isset($options['expanded']) && $options['expanded']) {
                            //radio buttons
                            return true;
                        }

                        //select tag
                        return false;
                    },

                    'template' => 'choice',
                    'multiple' => false,
                    'expanded' => false,
                    'model_manager' => null,
                    'class' => null,
                    'property' => null,
                    'query' => null,
                    'choices' => array(),
                    'preferred_choices' => array(),
                    'btn_add' => 'link_add',
                    'btn_list' => 'link_list',
                    'btn_delete' => 'link_delete',
                    'btn_catalogue' => 'SonataAdminBundle',

                    'tree' => null,
                    'current' => null,
                )
            )
        );


    }


    /**
     * @param Options $options
     *
     * @return array
     */
    public function getChoices(Options $options)
    {

        $tree = $options['tree'];

        $choices = array();

        foreach ($tree as $item) {
            
            if ($options['current'] && $options['current']->getId() === $item->getId()) {
                continue;
            }

            
            if (null !== $item->getParent()) {
                continue;
            }

            
            
            $choices[sprintf('%s', $item->getName())] = $item->getId();

            $this->childWalker($item, $options, $choices);
        }


        return $choices;
    }

    /**
     * @param         $category
     * @param Options $options
     * @param array $choices
     * @param int $level
     */
    private function childWalker($category, Options $options, array &$choices, $level = 2)
    {
        
        if ($category->getChildren() === null) {
            return;
        }

        
        foreach ($category->getChildren() as $child) {
            
            if ($options['current'] && $options['current']->getId() === $child->getId()) {
                continue;
            }

            
            $choices[sprintf('%s %s', str_repeat(' - - -', $level - 1), $child->getName())] = $child->getId();

            $this->childWalker($child, $options, $choices, $level + 1);
        }

    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'compo_tree_selector';
    }
}
