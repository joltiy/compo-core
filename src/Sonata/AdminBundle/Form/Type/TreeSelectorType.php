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
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Select a category.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class TreeSelectorType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $that = $this;

        $resolver->setDefaults(array(
            'tree' => null,

            'choice_list' => function (Options $opts, $previousValue) use ($that) {
                return new SimpleChoiceList($that->getChoices($opts));
            },
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
            if (!is_null($item->getParent())) {
                continue;
            }

            $choices[$item->getId()] = sprintf('%s', $item->getName());

            $this->childWalker($item, $options, $choices);
        }

        return $choices;
    }

    /**
     * @param Options $options
     * @param array   $choices
     * @param int     $level
     */
    private function childWalker($category, Options $options, array &$choices, $level = 2)
    {
        if ($category->getChildren() === null) {
            return;
        }

        foreach ($category->getChildren() as $child) {

            $choices[$child->getId()] = sprintf('%s %s', str_repeat(' - - -', $level - 1), $child);

            $this->childWalker($child, $options, $choices, $level + 1);
        }
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
