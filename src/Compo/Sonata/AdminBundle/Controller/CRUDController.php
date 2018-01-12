<?php

namespace Compo\Sonata\AdminBundle\Controller;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Pix\SortableBehaviorBundle\Services\PositionHandler;
use Sonata\AdminBundle\Controller\CRUDController as BaseCRUDController;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Sylius\Bundle\SettingsBundle\Form\Factory\SettingsFormFactoryInterface;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * {@inheritdoc}
 */
class CRUDController extends BaseCRUDController
{
    /**
     * The related Admin class.
     *
     * @var AbstractAdmin
     */
    protected $admin;

    /**
     * Return the admin related to the given $class.
     *
     * @param string $class
     *
     * @return \Sonata\AdminBundle\Admin\AdminInterface|null
     */
    public function getAdminByClass($class)
    {
        return $this->admin->getConfigurationPool()->getAdminByClass($class);
    }

    public function updateManyToManyAction(Request $request)
    {
        if (!$this->admin->isGranted('EDIT')) {
            throw new AccessDeniedException('EDIT');
        }

        $em = $this->getAdmin()->getDoctrine()->getManager();
        $ids = $request->request->get('value');
        $id = $request->request->get('pk');

        $object = $this->getAdmin()->getObject($id);

        $field = $request->request->get('field');

        $mm = $this->getAdmin()->getModelManager();

        /** @var \Doctrine\ORM\Mapping\ClassMetadata $ClassMetadata */
        $ClassMetadata = $mm->getMetadata($this->getAdmin()->getClass());

        $associationMapping = $ClassMetadata->getAssociationMapping($field);

        $items = $em->getRepository($associationMapping['targetEntity'])->findBy([
            'id' => $ids,
        ]);

        call_user_func_array([$object, 'set' . ucfirst($field)], [
            $items,
        ]);

        $this->getAdmin()->update($object);

        $result = [
        ];

        $associationAdmin = $this->getAdmin()->getConfigurationPool()->getAdminByClass($associationMapping['targetEntity']);

        foreach ($items as $item) {
            $result[] = [
                'id' => $item->getId(),
                'label' => $item->getName(),
                'edit_url' => $associationAdmin->generateObjectUrl('edit', $item),
            ];
        }

        return new JsonResponse([
            'items' => $result,
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function cloneAction()
    {
        $object = $this->admin->getSubject();

        if (false === $this->admin->hasAccess('edit')) {
            throw new AccessDeniedException();
        }

        if (false === $this->admin->hasAccess('create')) {
            throw new AccessDeniedException();
        }

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', 0));
        }

        $clonedObject = clone $object;

        if (method_exists($clonedObject, 'setName')) {
            $clonedObject->setName($object->getName() . ' (Копия)');
        }

        if (method_exists($clonedObject, 'setSlug')) {
            $clonedObject->setSlug('clone-slug-' . $clonedObject->getSlug());
        }

        $clonedObject->setId(null);

        $this->admin->create($clonedObject);

        $this->addFlash('sonata_flash_success', 'Создана копия');

        return new RedirectResponse($this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()]));

        // if you have a filtered list and want to keep your filters after the redirect
        // return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    public function historyRevertAction($id, $revision)
    {
        if (false === $this->admin->hasAccess('edit')) {
            throw new AccessDeniedException();
        }

        $id = $this->getRequest()->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            // check the csrf token
            $this->validateCsrfToken('sonata.history.revert');

            try {
                $manager = $this->get('sonata.admin.audit.manager');

                if (!$manager->hasReader($this->admin->getClass())) {
                    throw new NotFoundHttpException(sprintf('unable to find the audit reader for class : %s', $this->admin->getClass()));
                }

                $reader = $manager->getReader($this->admin->getClass());
                $reader->revert($object, $revision);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'ok']);
                }

                $this->addFlash('sonata_flash_info', $this->get('translator')->trans('flash_history_revert_successfull', [], 'PicossSonataExtraAdminBundle'));
            } catch (ModelManagerException $e) {
                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'error']);
                }

                $this->addFlash('sonata_flash_info', $this->get('translator')->trans('flash_history_revert_error', [], 'PicossSonataExtraAdminBundle'));
            }

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->render('@CompoSonataAdmin/CRUD/history_revert.html.twig', [
            'object' => $object,
            'revision' => $revision,
            'action' => 'revert',
            'csrf_token' => $this->getCsrfToken('sonata.history.revert'),
        ]);
    }

    /**
     * return the Response object associated to the trash action.
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     *
     * @return Response
     */
    public function trashAction(Request $request)
    {
        if (false === $this->admin->isGranted('UNDELETE')) {
            throw new AccessDeniedException();
        }

        $em = $this->get('doctrine')->getManager();

        if ($em->getFilters()->isEnabled('softdeleteable')) {
            $em->getFilters()->disable('softdeleteable');
        }

        if (!$em->getFilters()->isEnabled('softdeleteabletrash')) {
            $em->getFilters()->enable('softdeleteabletrash');
        }

        $em->getFilters()->getFilter('softdeleteabletrash')->enableForEntity($this->admin->getClass());

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('Symfony\Bridge\Twig\Extension\FormExtension')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render('@CompoSonataAdmin/CRUD/trash.html.twig', [
            'action' => 'trash',
            'form' => $formView,
            'datagrid' => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ]);
    }

    public function untrashAction(Request $request, $id)
    {
        if (false === $this->admin->hasAccess('undelete')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        if ($this->admin->getParent()) {
            $em = $this->admin->getModelManager()->getEntityManager($this->admin->getClass());

            $em->getFilters()->enable('softdeleteable');
            $em->getFilters()->enable('softdeleteabletrash');

            $em->getFilters()->getFilter('softdeleteable')->enableForEntity($this->admin->getParent()->getClass());
            $em->getFilters()->getFilter('softdeleteabletrash')->disableForEntity($this->admin->getParent()->getClass());

            $em->getFilters()->getFilter('softdeleteable')->disableForEntity($this->admin->getClass());
        } else {
            $em = $this->admin->getModelManager()->getEntityManager($this->admin->getClass());

            if ($em->getFilters()->isEnabled('softdeleteable')) {
                $em->getFilters()->disable('softdeleteable');
                $em->getFilters()->enable('softdeleteabletrash');
            }
        }

        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if ('POST' === $request->getMethod()) {
            // check the csrf token
            $this->validateCsrfToken('sonata.untrash');

            try {
                $object->setDeletedAt(null);
                $this->admin->update($object);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'ok']);
                }

                $this->addFlash('sonata_flash_info', $this->get('translator')->trans('flash_untrash_successfull', [], 'PicossSonataExtraAdminBundle'));
            } catch (ModelManagerException $e) {
                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'error']);
                }

                $this->addFlash('sonata_flash_info', $this->get('translator')->trans('flash_untrash_error', [], 'PicossSonataExtraAdminBundle'));
            }

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->render('@CompoSonataAdmin/CRUD/untrash.html.twig', [
            'object' => $object,
            'action' => 'untrash',
            'csrf_token' => $this->getCsrfToken('sonata.untrash'),
        ]);
    }

    /**
     * @param Request $request
     * @param string  $namespace
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function settingsAction(Request $request, $namespace = null)
    {
        if (false === $this->admin->hasAccess('settings')) {
            throw new AccessDeniedException();
        }

        if (null === $namespace) {
            $namespace = $this->admin->getSettingsNamespace();
        }

        $manager = $this->getSettingsManager();
        $settings = $manager->load($namespace);

        $form = $this
            ->getSettingsFormFactory()
            ->create($namespace);

        $form->setData($settings);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $manager->save($form->getData());

                $message = $this->getTranslator()->trans('settings.updated_successful');
                $this->get('session')->getFlashBag()->add('sonata_flash_success', $message);

                return $this->redirect($request->headers->get('referer'));
            }
        }

        $admin_pool = $this->get('sonata.admin.pool');

        return $this->render(
            'CompoCoreBundle:Admin:settings.html.twig',
            [
                'action' => 'settings',
                'breadcrumbs_builder' => $this->get('sonata.admin.breadcrumbs_builder'),
                'admin' => $this->admin,
                'base_template' => 'CompoSonataAdminBundle::standard_layout_compo.html.twig',

                'settings' => $settings,
                'form' => $form->createView(),
                'admin_pool' => $admin_pool,
                'translation_domain' => $this->admin->getTranslationDomain(),
            ]
        );
    }

    /**
     * @return SettingsManagerInterface
     */
    protected function getSettingsManager()
    {
        return $this->container->get('sylius.settings_manager');
    }

    /**
     * @return SettingsFormFactoryInterface
     */
    protected function getSettingsFormFactory()
    {
        return $this->container->get('sylius.form_factory.settings');
    }

    /**
     * @return TranslatorInterface
     */
    protected function getTranslator()
    {
        return $this->container->get('translator');
    }

    /**
     * List action.
     *
     * @return Response
     */
    public function sortableAction()
    {
        if (false === $this->admin->hasAccess('edit')) {
            throw new AccessDeniedException();
        }

        $request = $this->getRequest();

        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository($this->admin->getClass());

        $object = $repo->find($request->request->get('id'));

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        // Сдвигаем позицию назад, у всех кто после текущего

        $positionRelatedFields = $this->admin->getPostionRelatedFields();

        $qb->update($this->admin->getClass(), 'i')
            ->set('i.position', 'i.position - 1')
            ->where('i.position > ' . $object->getPosition());

        foreach ($positionRelatedFields as $field) {
            $qb->andWhere('i.' . $field . '= :position_' . $field);
            $qb->setParameter('position_' . $field, call_user_func_array([$object, 'get' . ucfirst($field)], []));
        }

        $qb->getQuery()->execute();

        // Получаем позицию элемента, после которого должен стоять текущий

        $after_object = $repo->find($request->request->get('after_id'));

        $after_pos = 0;

        if ($after_object) {
            $after_pos = $after_object->getPosition();
        }

        $new_pos = 0;

        // Если позиция не определена, то 1, иначе + 1 от позиции после которого должен стоять.
        if ($after_object) {
            $new_pos = $after_pos + 1;
        }

        // Обновляем позиции текущего и последующих

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->update($this->admin->getClass(), 'i')
            ->set('i.position', $new_pos)
            ->where('i.id = ' . $object->getId());

        foreach ($positionRelatedFields as $field) {
            $qb->andWhere('i.' . $field . '= :position_' . $field);
            $qb->setParameter('position_' . $field, call_user_func_array([$object, 'get' . ucfirst($field)], []));
        }

        $qb->getQuery()->execute();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->update($this->admin->getClass(), 'i')
            ->set('i.position', 'i.position + 1')
            ->where('i.position >= ' . $new_pos);

        foreach ($positionRelatedFields as $field) {
            $qb->andWhere('i.' . $field . '= :position_' . $field);
            $qb->setParameter('position_' . $field, call_user_func_array([$object, 'get' . ucfirst($field)], []));
        }

        $qb->getQuery()->execute();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->update($this->admin->getClass(), 'i')
            ->set('i.position', $new_pos)
            ->where('i.id = ' . $object->getId());

        foreach ($positionRelatedFields as $field) {
            $qb->andWhere('i.' . $field . '= :position_' . $field);
            $qb->setParameter('position_' . $field, call_user_func_array([$object, 'get' . ucfirst($field)], []));
        }

        $qb->getQuery()->execute();

        $this->admin->update($object);
        if ($after_object) {
            $this->admin->update($after_object);
        }

        return $this->renderJson(
            [
                'result' => 'ok',
                'objectId' => $this->admin->getNormalizedIdentifier($object),
            ]
        );
    }

    /**
     * Move element.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     *
     * @internal param string $position
     */
    public function moveAction(Request $request)
    {
        if (false === $this->admin->hasAccess('edit')) {
            throw new AccessDeniedException();
        }
        if ($this->admin->treeEnabled) {
            if (false === $this->admin->isGranted('EDIT')) {
                throw new AccessDeniedException();
            }

            if ($this->admin->isChild()) {
                $id = $request->query->get('childId');
            } else {
                $id = $request->query->get('id');
            }

            $targetId = $request->query->get('target');
            $dropPosition = $request->query->get('position');

            $em = $this->getDoctrine()->getManager();

            /** @var NestedTreeRepository $repo */
            $repo = $em->getRepository($this->admin->getClass());

            $currentNode = $repo->find($id);
            $targetNode = $repo->find($targetId);

            switch ($dropPosition) {
                case 'before':
                    $repo->persistAsPrevSiblingOf($currentNode, $targetNode);
                    break;
                case 'after':
                    $repo->persistAsNextSiblingOf($currentNode, $targetNode);
                    /*
                                        if ($currentNode->getLft() < $targetNode->getLft() ) {
                                            $repo->persistAsNextSiblingOf($currentNode, $targetNode);

                                        } else {
                                            $repo->persistAsNextSiblingOf($targetNode, $currentNode);
                                        }
                    */
                    break;
                case 'append':
                    $repo->persistAsLastChildOf($currentNode, $targetNode);
                    break;
            }

            $em->persist($targetNode);

            $em->persist($currentNode);

            $em->flush();

            $this->admin->update($currentNode);
            $this->admin->update($targetNode);

            $response = new Response(json_encode(['result' => true]), 200);

            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        $translator = $this->get('translator');

        if (!$this->admin->isGranted('EDIT')) {
            $this->addFlash(
                    'sonata_flash_error',
                    $translator->trans('flash_error_no_rights_update_position')
                );

            return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        }

        $object = $this->admin->getSubject();

        /** @var PositionHandler $positionService */
        $positionService = $this->get('pix_sortable_behavior.position');

        $entity = \Doctrine\Common\Util\ClassUtils::getClass($object);

        $lastPosition = $positionService->getLastPosition($entity);

        $position = $request->query->get('position');

        $position = $positionService->getPosition($object, $position, $lastPosition);

        $setter = sprintf('set%s', ucfirst($positionService->getPositionFieldByEntity($entity)));

        if (!method_exists($object, $setter)) {
            throw new \LogicException(
                    sprintf(
                        '%s does not implement ->%s() to set the desired position.',
                        $object,
                        $setter
                    )
                );
        }

        $object->{$setter}($position);

        $this->admin->update($object);

        if ($this->isXmlHttpRequest()) {
            return $this->renderJson(
                    [
                        'result' => 'ok',
                        'objectId' => $this->admin->getNormalizedIdentifier($object),
                    ]
                );
        }

        $this->addFlash(
                'sonata_flash_success',
                $translator->trans('flash_success_position_updated')
            );

        return new RedirectResponse(
                $this->admin->generateUrl(
                    'list',
                    ['filter' => $this->admin->getFilterParameters()]
                )
            );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request = null)
    {
        //if (isset($this->admin->treeEnabled) && $this->admin->treeEnabled) {
        //if (!$request->get('filter')) {
        //return new RedirectResponse($this->admin->generateUrl('tree', $request->query->all()));
        //}
        //}

        $filters = $request->query->get('filter', []);
        if ($this->getAdmin()->isChild() && $this->getAdmin()->getParentAssociationMapping()) {
            $name = str_replace('.', '__', $this->getAdmin()->getParentAssociationMapping());
            $val = ['value' => $request->get($this->getAdmin()->getParent()->getIdParameter())];

            if (ClassMetadataInfo::MANY_TO_MANY === $this->getAdmin()->getParentAssociationMappingType()) {
                $val = ['value' => [$request->get($this->getAdmin()->getParent()->getIdParameter())]];
            } else {
                $val = ['value' => $request->get($this->getAdmin()->getParent()->getIdParameter())];
            }

            if (isset($filters[$name]) && is_array($filters[$name]['value'])) {
                if (count($filters[$name]['value']) > 1) {
                    return new RedirectResponse(
                        $this->admin->getConfigurationPool()->getAdminByAdminCode($this->getAdmin()->getCode())->generateUrl('list', ['filter' => $filters])
                    );
                }

                if ($filters[$name]['value'][0] !== $request->get($this->getAdmin()->getParent()->getIdParameter())) {
                    return new RedirectResponse(
                        $this->admin->getConfigurationPool()->getAdminByAdminCode($this->getAdmin()->getCode())->generateUrl('list', ['filter' => $filters])
                    );
                }
            }
        }

        $listMode = $request->get('_list_mode');
        if ($listMode = $request->get('_list_mode')) {
            $this->admin->setListMode($listMode);
        }
        $listMode = $this->admin->getListMode();
        if ('tree' === $listMode) {
            if (isset($this->admin->treeEnabled) && $this->admin->treeEnabled) {
                $request = $this->getRequest();

                $this->admin->checkAccess('list');

                $preResponse = $this->preList($request);
                if (null !== $preResponse) {
                    return $preResponse;
                }

                if ($listMode = $request->get('_list_mode')) {
                    $this->admin->setListMode($listMode);
                }

                $datagrid = $this->admin->getDatagrid();
                $formView = $datagrid->getForm()->createView();

                return $this->render($this->admin->getTemplate('list'), [
                    'nodes' => $this->getTreeNodes($request),
                    'batch_action_forms' => $this->getBatchActionFormViews(),

                    'action' => 'list',
                    'form' => $formView,
                    'datagrid' => $datagrid,
                    'csrf_token' => $this->getCsrfToken('sonata.batch'),
                    'export_formats' =>
                        //$this->has('sonata.admin.admin_exporter') ?
                        //$this->get('sonata.admin.admin_exporter')->getAvailableFormats($this->admin) :
                        $this->admin->getExportFormats(),
                ], null);
            }

            return $this->listActionCustom($request);
        }

        return $this->listActionCustom($request);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function listActionCustom(Request $request = null)
    {
        $this->admin->checkAccess('list');

        $preResponse = $this->preList($request);
        if (null !== $preResponse) {
            return $preResponse;
        }

        if ($listMode = $request->get('_list_mode')) {
            $this->admin->setListMode($listMode);
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->setFormThemePublic($formView, $this->admin->getFilterTheme());

        return $this->render($this->admin->getTemplate('list'), [
            'action' => 'list',
            'form' => $formView,
            'batch_action_forms' => $this->getBatchActionFormViews(),

            'datagrid' => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
            'export_formats' =>
                //$this->has('sonata.admin.admin_exporter') ?
                //$this->get('sonata.admin.admin_exporter')->getAvailableFormats($this->admin) :
                $this->admin->getExportFormats(),
        ], null);
    }

    /**
     * @param $name
     *
     * @return FormBuilderInterface
     */
    public function createBatchActionForm($name)
    {
        return $this->get('form.factory')
            ->createNamedBuilder($name, 'form', [], [
                'label_format' => 'form.label_%name%',
                'translation_domain' => $this->admin->getTranslationDomain(),
            ]);
    }

    public function configureBatchActionForms()
    {
        $actionForms = [];

        return $actionForms;
    }

    public function getBatchActionFormViews()
    {
        $actionForms = [];

        foreach ($this->configureBatchActionForms() as $formName => $form) {
            $actionForms[$formName] = $form->getForm()->createView();
        }

        return $actionForms;
    }

    /**
     * Sets the admin form theme to form view. Used for compatibility between Symfony versions.
     *
     * @param FormView $formView
     * @param string   $theme
     */
    public function setFormThemePublic(FormView $formView, $theme)
    {
        $twig = $this->get('twig');

        try {
            $twig
                ->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')
                ->setTheme($formView, $theme);
        } catch (\Twig_Error_Runtime $e) {
            // BC for Symfony < 3.2 where this runtime not exists
            $twig
                ->getExtension('Symfony\Bridge\Twig\Extension\FormExtension')
                ->renderer
                ->setTheme($formView, $theme);
        }
    }

    public function getTreeNodes($request)
    {
        // set the theme for the current Admin Form
        //$this->setFormTheme($formView, $this->admin->getFilterTheme());
        $em = $this->getDoctrine()->getManager();

        /** @var NestedTreeRepository $repo */
        $repo = $em->getRepository($this->admin->getClass());

        return $repo->childrenHierarchyWithNodes();
    }

    /**
     * @param \Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery $selectedModelQuery
     * @param Request                                            $request
     *
     * @throws ModelManagerException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return RedirectResponse
     */
    public function batchActionDisable(\Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery $selectedModelQuery, Request $request = null)
    {
        if (!$this->admin->isGranted('EDIT')) {
            throw new AccessDeniedException();
        }

        /** @var QueryBuilder $selectedModelQuery */

        /** @var ModelManager $modelManager */
        $modelManager = $this->admin->getModelManager();

        $aliases = $selectedModelQuery->getRootAliases();

        $selectedModelQuery->select('DISTINCT ' . $aliases[0]);

        try {
            $entityManager = $modelManager->getEntityManager($this->admin->getClass());

            $i = 0;
            foreach ($selectedModelQuery->getQuery()->iterate() as $pos => $object) {
                $object[0]->setEnabled(false);
                $modelManager->update($object[0]);

                if (0 === (++$i % 100)) {
                    $entityManager->flush();
                    $entityManager->clear();
                }
            }

            $entityManager->flush();
            $entityManager->clear();
        } catch (\PDOException $e) {
            throw new ModelManagerException('', 0, $e);
        } catch (DBALException $e) {
            throw new ModelManagerException('', 0, $e);
        }

        $this->addFlash('sonata_flash_success', 'flash_batch.disable_success');

        return new RedirectResponse(
            $this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()])
        );
    }

    /**
     * @param \Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery $selectedModelQuery
     * @param Request                                            $request
     *
     * @return RedirectResponse
     */
    public function batchActionEnable2(\Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery $selectedModelQuery, Request $request = null)
    {
        if (!$this->admin->isGranted('EDIT')) {
            throw new AccessDeniedException();
        }

        /** @var QueryBuilder $qb */
        $qb = $selectedModelQuery->getQueryBuilder();

        /* @var QueryBuilder $selectedModelQuery */
        $selectedModelQuery->select($qb->getRootAliases()[0] . '.id');

        $result = $selectedModelQuery->execute([], Query::HYDRATE_ARRAY);

        $ids = [];

        foreach ($result as $result_item) {
            $ids[] = $result_item['id'];
        }

        $chunks = array_chunk($ids, 500);

        $repository = $this->getAdmin()->getRepository();

        $class = $repository->getClassName();

        $em = $this->getAdmin()->getDoctrine()->getManager();

        foreach ($chunks as $chunksIds) {
            /** @var Query $q */
            $q = $em->createQuery('UPDATE ' . $class . ' o SET o.enabled = 1 WHERE o.id IN(' . implode(',', $chunksIds) . ')');
            $q->execute();
        }

        $this->addFlash('sonata_flash_success', 'flash_batch.enable_success');

        return new RedirectResponse(
            $this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()])
        );
    }

    /**
     * @return AbstractAdmin
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param AbstractAdmin $admin
     */
    public function setAdmin(AbstractAdmin $admin)
    {
        $this->admin = $admin;
    }

    /**
     * @param \Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery $selectedModelQuery
     * @param Request                                            $request
     *
     * @throws ModelManagerException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return RedirectResponse
     */
    public function batchActionEnable(\Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery $selectedModelQuery, Request $request = null)
    {
        if (!$this->admin->isGranted('EDIT')) {
            throw new AccessDeniedException();
        }

        /** @var ModelManager $modelManager */
        $modelManager = $this->admin->getModelManager();

        /* @var QueryBuilder $selectedModelQuery */
        $selectedModelQuery->select('DISTINCT ' . $selectedModelQuery->getRootAliases()[0]);

        try {
            $entityManager = $modelManager->getEntityManager($this->admin->getClass());

            $i = 0;
            foreach ($selectedModelQuery->getQuery()->iterate() as $pos => $object) {
                $object[0]->setEnabled(true);
                $modelManager->update($object[0]);

                if (0 === (++$i % 100)) {
                    $entityManager->flush();
                    $entityManager->clear();
                }
            }

            $entityManager->flush();
            $entityManager->clear();
        } catch (\PDOException $e) {
            throw new ModelManagerException('', 0, $e);
        } catch (DBALException $e) {
            throw new ModelManagerException('', 0, $e);
        }

        $this->addFlash('sonata_flash_success', 'flash_batch.enable_success');

        return new RedirectResponse(
            $this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()])
        );
    }

    /**
     * Check that user can change given schema.
     *
     * @param string $schemaAlias
     *
     * @return bool
     */
    protected function isGrantedOr403($schemaAlias)
    {
        if (!$this->container->has('sylius.authorization_checker')) {
            return true;
        }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isApiRequest(Request $request)
    {
        return 'html' !== $request->getRequestFormat();
    }

    /**
     * Contextualize the admin class depends on the current request.
     *
     * @throws \RuntimeException
     */
    protected function configure()
    {
        parent::configure();

        if (isset($this->admin->treeEnabled) && $this->admin->treeEnabled) {
            $this->admin->setTemplate('tree', 'CompoSonataAdminBundle:Tree:tree.html.twig');
            //$this->admin->setTemplate('list', 'CompoSonataAdminBundle:Tree:list.html.twig');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function redirectTo($object)
    {
        $request = $this->getRequest();

        $url = false;

        if (null !== $request->get('btn_update_and_list')) {
            $url = $this->admin->generateUrl('list');
        }
        if (null !== $request->get('btn_create_and_list')) {
            $url = $this->admin->generateUrl('list');
        }

        if (null !== $request->get('btn_create_and_create')) {
            $params = [];
            if ($this->admin->hasActiveSubClass()) {
                $params['subclass'] = $request->get('subclass');
            }
            $url = $this->admin->generateUrl('create', $params);
        }

        if ('DELETE' === $this->getRestMethod()) {
            $url = $this->admin->generateUrl('list');
        }

        if (!$url) {
            foreach (['edit', 'show'] as $route) {
                if ($this->admin->hasRoute($route) && $this->admin->isGranted(mb_strtoupper($route), $object)) {
                    $params = [];

                    if ('edit' === $route) {
                        $params['current_tab_index'] = $request->get('current_tab_index');
                    }
                    $url = $this->admin->generateObjectUrl($route, $object, $params);
                    break;
                }
            }
        }

        if (!$url) {
            $url = $this->admin->generateUrl('list');
        }

        return new RedirectResponse($url);
    }
}
