<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Form;

use Doctrine\Common\Util\ClassUtils;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Sonata\CoreBundle\Model\Adapter\AdapterInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class ModelChoiceLoader implements ChoiceLoaderInterface
{
    /**
     * @var array
     */
    public $identifier;

    /**
     * @var \Sonata\AdminBundle\Model\ModelManagerInterface
     */
    private $modelManager;

    /**
     * @var string
     */
    private $class;

    /**
     * @var null
     */
    private $property;

    /**
     * @var null
     */
    private $query;

    /**
     * @var array
     */
    private $choices;
    /**
     * @var
     */
    private $options;

    /**
     * @var PropertyPath
     */
    private $propertyPath;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var
     */
    private $choiceList;

    /**
     * @param ModelManagerInterface $modelManager
     * @param string $class
     * @param null $property
     * @param null $query
     * @param array $choices
     * @param PropertyAccessorInterface|null $propertyAccessor
     * @param $options
     */
    public function __construct(ModelManagerInterface $modelManager, $class, $property = null, $query = null, array $choices = array(), PropertyAccessorInterface $propertyAccessor = null, $options)
    {
        $this->modelManager = $modelManager;
        $this->class = $class;
        $this->property = $property;
        $this->query = $query;
        $this->choices = $choices;
        $this->options = $options;

        $this->identifier = $this->modelManager->getIdentifierFieldNames($this->class);

        // The property option defines, which property (path) is used for
        // displaying entities as strings
        if ($property) {
            $this->propertyPath = new PropertyPath($property);
            $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        if (!$this->choiceList) {
            if ($this->query) {
                $entities = $this->modelManager->executeQuery($this->query);
            } elseif (is_array($this->choices) && count($this->choices) > 0) {
                $entities = $this->choices;
            } else {
                $entities = $this->modelManager->findBy($this->class);
            }

            $choices = array();
            foreach ($entities as $key => $entity) {
                if ($this->propertyPath) {
                    // If the property option was given, use it
                    $valueObject = $this->propertyAccessor->getValue($entity, $this->propertyPath);
                } else {
                    // Otherwise expect a __toString() method in the entity
                    try {
                        $valueObject = (string)$entity;
                    } catch (\Exception $e) {
                        throw new RuntimeException(sprintf('Unable to convert the entity "%s" to string, provide "property" option or implement "__toString()" method in your entity.', ClassUtils::getClass($entity)), 0, $e);
                    }
                }

                $id = implode(AdapterInterface::ID_SEPARATOR, $this->getIdentifierValues($entity));

                if (!array_key_exists($valueObject, $choices)) {
                    $choices[$valueObject] = array();
                }

                $choices[$valueObject][] = $id;
            }

            //$finalChoices = array();


            $tree = $this->options['tree'];

            $choices = array();

            foreach ($tree as $item) {
                if ($this->options['current'] && $this->options['current']->getId() === $item->getId()) {
                    continue;
                }

                
                if (null !== $item->getParent()) {
                    continue;
                }

                
                
                $choices[sprintf('%s', $item->getName())] = $item->getId();

                $this->childWalker($item, $this->options, $choices);
            }


            $finalChoices = $choices;


            $this->choiceList = new ArrayChoiceList($finalChoices, $value);
        }

        return $this->choiceList;
    }

    /**
     * @param object $entity
     *
     * @return array
     */
    private function getIdentifierValues($entity)
    {
        try {
            return $this->modelManager->getIdentifierValues($entity);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(sprintf('Unable to retrieve the identifier values for entity %s', ClassUtils::getClass($entity)), 0, $e);
        }
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
    public function loadValuesForChoices(array $choices, $value = null)
    {
        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }
}
