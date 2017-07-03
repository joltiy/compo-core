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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Select a category.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class TreeSelectorType extends AbstractType
{
    /** @noinspection PhpDeprecationInspection */
    /** @noinspection PhpDeprecationInspection */

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $that = $this;

        /** @noinspection PhpUnusedParameterInspection */
        $resolver->setDefaults(array(
            'tree' => null,
            'current' => null,

            'choice_list' => function (Options $opts, $previousValue) use ($that) {
                return new ArrayChoiceList($that->getChoices($opts));
            },
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'tree' => null,
            'current' => null,
            'choice_list' => array()
        ));
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
            /** @noinspection PhpUndefinedMethodInspection */
            if ($options['current'] && $options['current']->getId() == $item->getId()) {
                continue;
            }

            /** @noinspection PhpUndefinedMethodInspection */
            if (!is_null($item->getParent())) {
                continue;
            }

            /** @noinspection PhpUndefinedMethodInspection */
            /** @noinspection PhpUndefinedMethodInspection */
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
        /** @noinspection PhpUndefinedMethodInspection */
        if ($category->getChildren() === null) {
            return;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        foreach ($category->getChildren() as $child) {
            /** @noinspection PhpUndefinedMethodInspection */
            if ($options['current'] && $options['current']->getId() == $child->getId()) {
                continue;
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $choices[sprintf('%s %s', str_repeat(' - - -', $level - 1), $child->getName())] = $child->getId();

            $this->childWalker($child, $options, $choices, $level + 1);
        }

    }

    public function getChoiceLabel($value, $key, $index)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'sonata_type_model';
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
