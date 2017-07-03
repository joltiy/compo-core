<?php

namespace Compo\Sonata\BlockBundle\Block\Service;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\Service\AbstractBlockService as BaseAbstractBlockService;
use Sonata\CoreBundle\Model\Metadata;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Model\BlockInterface;

/**
 * {@inheritDoc}
 */
class AbstractBlockService extends BaseAbstractBlockService
{
    use ContainerAwareTrait;

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->getContainer()->get('request_stack')->getCurrentRequest();
    }

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
        $this->updateName($object);
    }

    public function updateName($object) {
        $name = $object->getName();

        if (!$name) {
            /** @var Metadata $blockMetadata */
            $blockMetadata = $this->getBlockMetadata();

            $name = $this->getContainer()->get('translator')->trans($blockMetadata->getTitle(), array(), $blockMetadata->getDomain());

            $object->setName($name);
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
        $this->updateName($object);

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

        $this->updateLabel($formMapper);
    }

    /**
     * {@inheritdoc}
     */
    public function buildCreateForm(FormMapper $formMapper, BlockInterface $block)
    {
        $this->buildForm($formMapper, $block);

        $this->updateLabel($formMapper);
    }

    public function updateLabel(FormMapper $formMapper) {

        $settings = $formMapper->get('settings');

        $settingsKeys = $settings->getOption('keys');

        $translation_domain = $this->getBlockMetadata()->getDomain();

        foreach ($settingsKeys as $key => $settingsKeysItem) {
            if (!isset($settingsKeysItem[2])) {
                $settingsKeysItem[2] = array();
            }

            if (!isset($settingsKeysItem[2]['label'])) {
                $settingsKeysItem[2]['label'] = 'form.label_' . $settingsKeysItem[0];
            }

            if (!isset($settingsKeysItem[2]['translation_domain'])) {
                $settingsKeysItem[2]['translation_domain'] = $translation_domain;
            }

            $settingsKeys[$key] = $settingsKeysItem;
        }

        $formMapper->add('temp', 'text');

        $formMapper->remove('settings');

        $formMapper->add('settings', 'sonata_type_immutable_array', array('keys' => $settingsKeys), array('translation_domain' => $translation_domain));

        $formMapper->remove('temp');

    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        $name = $this->getName();

        $nameArray = explode('.', $name);

        $domainArray = explode('_', $nameArray[0]);

        foreach ($domainArray as $key => $value) {
            $domainArray[$key] = ucfirst($value);
        }

        $domain = implode('', $domainArray) . 'Bundle';

        return new Metadata('block.title_' . $nameArray[3], $this->getName(), false, $domain, array(
            'class' => 'fa fa-file-text-o',
        ));
    }
}