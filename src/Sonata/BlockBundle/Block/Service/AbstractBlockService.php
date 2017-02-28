<?php


namespace Compo\Sonata\BlockBundle\Block\Service;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\Service\AbstractBlockService as BaseAbstractBlockService;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Model\BlockInterface;

class AbstractBlockService extends BaseAbstractBlockService
{
    use ContainerAwareTrait;
    /**
     * @param ErrorElement $errorElement
     * @param BlockInterface $block
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {

    }

    /**
     * @param $object
     */
    public function postPersist($object)
    {

    }

    /**
     * @param $object
     */
    public function prePersist($object)
    {
        $name = $object->getName();

        if (!$name) {

        }

    }

    /**
     * @param $object
     */
    public function preRemove($object)
    {

    }

    /**
     * @param $object
     */
    public function postRemove($object)
    {

    }

    /**
     * @param $object
     */
    public function preUpdate($object)
    {

    }

    /**
     * @param $object
     */
    public function postUpdate($object)
    {

    }

    /**
     * @param FormMapper $formMapper
     * @param BlockInterface $block
     */
    public function buildForm(FormMapper $formMapper, BlockInterface $block)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $this->buildForm($formMapper, $block);
    }

    /**
     * {@inheritdoc}
     */
    public function buildCreateForm(FormMapper $formMapper, BlockInterface $block)
    {
        $this->buildForm($formMapper, $block);
    }

}