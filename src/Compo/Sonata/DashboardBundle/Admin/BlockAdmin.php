<?php

declare(strict_types=1);

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\DashboardBundle\Admin;

use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\BlockBundle\Block\BlockServiceManagerInterface;
use Sonata\BlockBundle\Form\Type\ServiceListType;
use Sonata\Cache\CacheManagerInterface;
use Sonata\DashboardBundle\Entity\BaseBlock;
use Sonata\DashboardBundle\Model\DashboardInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract admin class for the Block model.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class BlockAdmin extends \Compo\Sonata\AdminBundle\Admin\AbstractAdmin
{
    /**
     * @var BlockServiceManagerInterface
     */
    protected $blockManager;

    /**
     * @var CacheManagerInterface
     */
    protected $cacheManager;

    /**
     * @var bool
     */
    protected $inValidate = false;

    /**
     * @var array
     */
    protected $containerBlockTypes = [];

    /**
     * @var string
     */
    protected $defaultContainerType;

    /**
     * Конфигурация админки.
     */
    public function configure()
    {
        $this->parentAssociationMapping = 'dashboard';
    }

    /**
     * @return array
     */
    protected function getAccess()
    {
        return array_merge([
            'savePosition' => 'EDIT',
            'switchParent' => 'EDIT',
            'composePreview' => 'EDIT',
        ], parent::getAccess());
    }

    /**
     * {@inheritdoc}
     */
    public function getObject($id)
    {
        $subject = parent::getObject($id);

        if ($subject) {
            /** @var AbstractBlockService $service */
            $service = $this->blockManager->get($subject);

            $resolver = new OptionsResolver();
            $service->configureSettings($resolver);

            try {
                $subject->setSettings($resolver->resolve($subject->getSettings()));
            } catch (InvalidOptionsException $e) {
            }

            $service->load($subject);
        }

        return $subject;
    }

    /**
     * {@inheritdoc}
     *
     * @param BaseBlock $object
     */
    public function preUpdate($object): void
    {
        /** @var BlockAdmin $block */
        $block = $this->blockManager->get($object);

        $block->preUpdate($object);

        // fix weird bug with setter object not being call
        $object->setChildren($object->getChildren());

        $dashboard = $object->getDashboard();

        if ($dashboard instanceof DashboardInterface) {
            $dashboard->setEdited(true);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param BaseBlock $object
     */
    public function postUpdate($object): void
    {
        /** @var BlockAdmin $block */
        $blockManager = $this->blockManager;

        $block = $blockManager->get($object);
        $block->postUpdate($object);

        $service = $blockManager->get($object);

        $this->cacheManager->invalidate($service->getCacheKeys($object));
    }

    /**
     * {@inheritdoc}
     *
     * @param BaseBlock $object
     */
    public function prePersist($object): void
    {
        /** @var BlockAdmin $block */
        $block = $this->blockManager->get($object);
        $block->prePersist($object);

        $dashboard = $object->getDashboard();

        if ($dashboard instanceof DashboardInterface) {
            $dashboard->setEdited(true);
        }

        // fix weird bug with setter object not being call
        $object->setChildren($object->getChildren());
    }

    /**
     * {@inheritdoc}
     *
     * @param BaseBlock $object
     */
    public function postPersist($object): void
    {
        /** @var BlockAdmin $block */
        $blockManager = $this->blockManager;

        $block = $blockManager->get($object);
        $block->postPersist($object);

        $service = $blockManager->get($object);

        $this->cacheManager->invalidate($service->getCacheKeys($object));
    }

    /**
     * {@inheritdoc}
     *
     * @param BaseBlock $object
     */
    public function preRemove($object): void
    {
        /** @var BlockAdmin $block */
        $block = $this->blockManager->get($object);
        $block->preRemove($object);
    }

    /**
     * {@inheritdoc}
     *
     * @param BaseBlock $object
     */
    public function postRemove($object): void
    {
        /** @var BlockAdmin $block */
        $block = $this->blockManager->get($object);
        $block->postRemove($object);
    }

    /**
     * @param BlockServiceManagerInterface $blockManager
     */
    public function setBlockManager(BlockServiceManagerInterface $blockManager): void
    {
        $this->blockManager = $blockManager;
    }

    /**
     * @param CacheManagerInterface $cacheManager
     */
    public function setCacheManager(CacheManagerInterface $cacheManager): void
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param array $containerBlockTypes
     */
    public function setContainerBlockTypes(array $containerBlockTypes): void
    {
        $this->containerBlockTypes = $containerBlockTypes;
    }

    /**
     * @param $defaultContainerType
     */
    public function setDefaultContainerType($defaultContainerType): void
    {
        $this->defaultContainerType = $defaultContainerType;
    }

    /**
     * @return string
     */
    public function getDefaultContainerType()
    {
        return $this->defaultContainerType;
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters()
    {
        if (!$this->hasRequest()) {
            return [];
        }

        $request = $this->getRequest();

        $parameters = parent::getPersistentParameters();

        $composer = $request->get('composer');

        if ($composer) {
            $parameters['composer'] = $composer;
        }

        $parameters['type'] = $request->get('type');

        return $parameters;
    }

    /**
     * Override needed to make the dashboard composer cleaner.
     *
     * {@inheritdoc}
     */
    public function toString($object)
    {
        if (!\is_object($object)) {
            return '';
        }
        if (method_exists($object, 'getName') && null !== $object->getName()) {
            return (string) $object->getName();
        }

        return parent::toString($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('type')
            ->add('name')
            ->add('enabled', null, ['editable' => true])
            ->add('updatedAt')
            ->add('position')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('enabled')
            ->add('type')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection): void
    {
        parent::configureRoutes($collection);

        $collection->add('view', $this->getRouterIdParameter() . '/view');
        $collection->add('switchParent', 'switch-parent');
        $collection->add('savePosition', '{block_id}/save-position', [
            'block_id' => null,
        ]);
        $collection->add('composePreview', '{block_id}/compose_preview', [
            'block_id' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $block = $this->getSubject();

        $dashboard = false;

        $parent = $this->getParent();

        if ($parent) {
            $dashboard = $parent->getSubject();

            if (!$dashboard instanceof DashboardInterface) {
                throw new \RuntimeException('The BlockAdmin must be attached to a parent DashboardAdmin');
            }

            if (null === $block->getId()) { // new block
                $block->setType($this->request->get('type'));
                $block->setDashboard($dashboard);
            }

            if ($block->getDashboard()->getId() !== $dashboard->getId()) {
                throw new \RuntimeException('The dashboard reference on BlockAdmin and parent admin are not the same');
            }
        }

        $isComposer = $this->hasRequest() ? $this->getRequest()->get('composer', false) : false;

        $generalGroupOptions = $optionsGroupOptions = [];

        if ($isComposer) {
            //$generalGroupOptions['class'] = 'hidden';
            $optionsGroupOptions['name'] = '';
        }

        $formMapper->with('form.field_group_general', $generalGroupOptions);

        $containerBlockTypes = $this->containerBlockTypes;

        $isContainerRoot = $block && \in_array($block->getType(), $containerBlockTypes, true) && !$this->hasParentFieldDescription();
        $isStandardBlock = $block && !\in_array($block->getType(), $containerBlockTypes, true) && !$this->hasParentFieldDescription();

        if (!$isComposer) {
            $formMapper->add('name');
        } elseif (!$isContainerRoot) {
            $formMapper->add('name', HiddenType::class);

            $formMapper->add('cssClass', null, ['required' => false]);
        }

        $formMapper->end();

        if ($isContainerRoot || $isStandardBlock) {
            $formMapper->with('form.field_group_general', $generalGroupOptions);

            /** @var AbstractBlockService $service */
            $service = $this->blockManager->get($block);

            // need to investigate on this case where $dashboard == null ... this should not be possible
            if ($isStandardBlock && $dashboard && !empty($containerBlockTypes)) {
                $formMapper->add('parent', EntityType::class, [
                    'class' => $this->getClass(),
                    'query_builder' => function (EntityRepository $repository) use ($dashboard, $containerBlockTypes) {
                        return $repository->createQueryBuilder('a')
                            ->andWhere('a.dashboard = :dashboard AND a.type IN (:types)')
                            ->setParameters([
                                'dashboard' => $dashboard,
                                'types' => $containerBlockTypes,
                            ]);
                    },
                ], [
                    'admin_code' => $this->getCode(),
                ]);
            }

            if ($isComposer) {
                $formMapper->add('enabled', HiddenType::class, [
                    'data' => true,
                ]);
            } else {
                $formMapper->add('enabled');
            }

            if ($isStandardBlock) {
                $formMapper->add('position', IntegerType::class);
            }

            $formMapper->end();

            $formMapper->with('form.field_group_options', $optionsGroupOptions);

            if ($block->getId() > 0) {
                $service->buildEditForm($formMapper, $block);
            } else {
                $service->buildCreateForm($formMapper, $block);
            }

            // When editing a container in composer view, hide some settings
            if ($isContainerRoot && $isComposer) {
                $formMapper->remove('children');
                $formMapper->add('name', TextType::class, [
                    'required' => true,
                ]);

                $formSettings = $formMapper->get('settings');

                $formSettings->remove('code');
                $formSettings->remove('layout');
                $formSettings->remove('template');
            }

            $formMapper->end();
        } else {
            $formMapper
                ->with('form.field_group_options', $optionsGroupOptions)
                    ->add('type', ServiceListType::class, [
                        'context' => 'sonata_dashboard_bundle',
                    ])
                    ->add('enabled')
                    ->add('position', IntegerType::class)
                ->end()
            ;
        }
    }
}
