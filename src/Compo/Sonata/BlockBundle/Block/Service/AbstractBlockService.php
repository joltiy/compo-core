<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\BlockBundle\Block\Service;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\Service\AbstractBlockService as BaseAbstractBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Model\Metadata;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\PageBundle\Model\Block;

/**
 * {@inheritdoc}
 */
class AbstractBlockService extends BaseAbstractBlockService
{
    use ContainerAwareTrait;

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getEntityManager()
    {
        $container = $this->getContainer();

        return $container->get('doctrine')->getManager();
    }

    /**
     * @param $entityClass
     *
     * @return \Sonata\AdminBundle\Admin\AdminInterface
     */
    public function getAdminByClass($entityClass)
    {
        $container = $this->getContainer();

        return $container->get('sonata.admin.pool')->getAdminByClass($entityClass);
    }

    /**
     * @param $entityClass
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository($entityClass)
    {
        $container = $this->getContainer();

        $em = $container->get('doctrine')->getManager();

        /* @var EntityRepository $repository */
        return $em->getRepository($entityClass);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->getContainer()->get('request_stack')->getCurrentRequest();
    }

    /**
     * @param ErrorElement   $errorElement
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

    /**
     * @param $object
     */
    public function updateName($object)
    {
        /** @var Block $object */
        $name = $object->getName();

        if (!$name) {
            /** @var Metadata $blockMetadata */
            $blockMetadata = $this->getBlockMetadata();

            $name = $this->getContainer()->get('translator')->trans($blockMetadata->getTitle(), [], $blockMetadata->getDomain());

            $object->setName($name);
        }
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

        return new Metadata(
            'block.title_' . $nameArray[3], $this->getName(), false, $domain, [
                'class' => 'fa fa-file-text-o',
            ]
        );
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
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $this->buildForm($formMapper, $block);

        $this->updateLabel($formMapper);
    }

    /**
     * @param FormMapper     $formMapper
     * @param BlockInterface $block
     */
    public function buildForm(FormMapper $formMapper, BlockInterface $block)
    {
    }

    /**
     * @param FormMapper $formMapper
     */
    public function updateLabel(FormMapper $formMapper)
    {
        $settings = $formMapper->get('settings');

        /** @var array $settingsKeys */
        $settingsKeys = $settings->getOption('keys');

        $translation_domain = $this->getBlockMetadata()->getDomain();

        foreach ($settingsKeys as $key => $settingsKeysItem) {
            if (!isset($settingsKeysItem[2])) {
                $settingsKeysItem[2] = [];
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

        $formMapper->add('settings', 'sonata_type_immutable_array', ['keys' => $settingsKeys], ['translation_domain' => $translation_domain]);

        $formMapper->remove('temp');
    }

    /**
     * {@inheritdoc}
     */
    public function buildCreateForm(FormMapper $formMapper, BlockInterface $block)
    {
        $this->buildForm($formMapper, $block);

        $this->updateLabel($formMapper);
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getDoctrineManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @param BlockInterface $block
     *
     * @return array
     */
    public function getCacheKeys(BlockInterface $block)
    {
        $settings = $block->getSettings();

        $keys = parent::getCacheKeys($block);

        $keys['environment'] = $this->getContainer()->get('kernel')->getEnvironment();

        if (isset($settings['template'])) {
            $keys['template'] = $settings['template'];
        }

        if (isset($settings['id'])) {
            $key = $this->getName() . ':' . $settings['id'];

            if (isset($settings['template'])) {
                $key = $key . ':' . $settings['template'];
            }

            $keys['block_id'] = $key;
        }

        $keys['updated_at'] = '';

        return $keys;
    }
}
