<?php
/**
 * Created by PhpStorm.
 * User: jivoy1988
 * Date: 31.05.16
 * Time: 9:53
 */

namespace Compo\Sonata\AdminBundle\Controller;

use Compo\Sonata\AdminBundle\Admin\Admin;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Pix\SortableBehaviorBundle\Services\PositionHandler;
use Sonata\AdminBundle\Controller\CRUDController as BaseCRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * {@inheritDoc}
 */
class CRUDController extends BaseCRUDController
{
    /**
     * The related Admin class.
     *
     * @var Admin
     */
    protected $admin;

    /**
     * List action.
     *
     * @return Response
     *
     */
    public function sortableAction()
    {
        $request = $this->getRequest();

        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository($this->admin->getClass());

        $object = $repo->find($request->get('id'));

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        // Сдвигаем позицию назад, у всех кто после текущего


        $qb->update($this->admin->getClass(), 'i')
            ->set('i.position', 'i.position - 1')
            ->where('i.position > ' . $object->getPosition());

        $qb->getQuery()->execute();

        // Получаем позицию элемента, после которого должен стоять текущий


        $after_object = $repo->find($request->get('after_id'));

        if ($after_object) {
            $after_pos = $after_object->getPosition();
        } else {
            $after_pos = 0;
        }

        // Если позиция не определена, то 1, иначе + 1 от позиции после которого должен стоять.
        if ($after_object) {
            $new_pos = $after_pos + 1;
        } else {
            $new_pos = 0;
        }

        // Обновляем позиции текущего и последующих

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->update($this->admin->getClass(), 'i')
            ->set('i.position', $new_pos)
            ->where('i.id = ' . $object->getId());
        $qb->getQuery()->execute();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->update($this->admin->getClass(), 'i')
            ->set('i.position', 'i.position + 1')
            ->where('i.position >= ' . $new_pos);
        $qb->getQuery()->execute();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->update($this->admin->getClass(), 'i')
            ->set('i.position', $new_pos)
            ->where('i.id = ' . $object->getId());


        $qb->getQuery()->execute();

        return $this->renderJson(array(
            'result' => 'ok',
            'objectId' => $this->admin->getNormalizedIdentifier($object)
        ));

    }


    /**
     * Move element
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     * @internal param string $position
     */
    public function moveAction(Request $request)
    {
        if ($this->admin->treeEnabled) {
            if (false === $this->admin->isGranted('EDIT')) {
                throw new AccessDeniedException();
            }
            $id = $request->query->get('id');
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

            $response = new Response(json_encode(array('result' => true)), 200);

            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
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
                return $this->renderJson(array(
                    'result' => 'ok',
                    'objectId' => $this->admin->getNormalizedIdentifier($object)
                ));
            }

            $this->addFlash(
                'sonata_flash_success',
                $translator->trans('flash_success_position_updated')
            );

            return new RedirectResponse($this->admin->generateUrl(
                'list',
                array('filter' => $this->admin->getFilterParameters())
            ));
        }
    }


    /**
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request = null)
    {
        if (isset($this->admin->treeEnabled) && $this->admin->treeEnabled) {
            if (!$request->get('filter')) {
                return new RedirectResponse($this->admin->generateUrl('tree'));
            }
        }

        return parent::listAction();
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function treeAction(Request $request = null)
    {
        $request->getBasePath();

        if (isset($this->admin->treeEnabled) && $this->admin->treeEnabled) {

            if (false === $this->admin->isGranted('LIST')) {
                throw new AccessDeniedException();
            }
            $em = $this->getDoctrine()->getManager();

            /** @var NestedTreeRepository $repo */
            $repo = $em->getRepository($this->admin->getClass());
            $tree = $repo->childrenHierarchy();

            return $this->render($this->admin->getTemplate('tree'), array(
                'action' => 'tree',
                'nodes' => $tree,
                'form' => $this->admin->getDatagrid()->getForm()->createView(),
            ));
        } else {
            return parent::listAction();
        }

    }

    /**
     * @return Admin
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param Admin $admin
     */
    public function setAdmin(Admin $admin)
    {
        $this->admin = $admin;
    }

    /**
     * Contextualize the admin class depends on the current request
     *
     * @throws \RuntimeException
     */
    protected function configure()
    {
        parent::configure();

        if (isset($this->admin->treeEnabled) && $this->admin->treeEnabled) {
            $this->admin->setTemplate('tree', 'CompoSonataAdminBundle:Tree:tree.html.twig');
            $this->admin->setTemplate('list', 'CompoSonataAdminBundle:Tree:list.html.twig');
        }
    }

    /**
     * {@inheritDoc}
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
            $params = array();
            if ($this->admin->hasActiveSubClass()) {
                $params['subclass'] = $request->get('subclass');
            }
            $url = $this->admin->generateUrl('create', $params);
        }

        if ($this->getRestMethod() === 'DELETE') {
            $url = $this->admin->generateUrl('list');
        }

        if (!$url) {
            foreach (array('edit', 'show') as $route) {
                if ($this->admin->hasRoute($route) && $this->admin->isGranted(strtoupper($route), $object)) {
                    $params = array();

                    if ($route == 'edit') {
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