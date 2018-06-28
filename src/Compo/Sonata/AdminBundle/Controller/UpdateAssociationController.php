<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Controller;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * AJAX обновление связей ManyToMany/ManyToOne.
 */
class UpdateAssociationController extends CRUDController
{
    /**
     * Обновление связей ManyToMany.
     *
     * @param Request $request
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     *
     * @return JsonResponse
     */
    public function updateManyToManyAction(Request $request)
    {
        /** @var AbstractAdmin $admin */
        $admin = $this->getAdmin();

        $admin->checkAccess('edit');

        $em = $admin->getDoctrine()->getManager();

        $ids = $request->request->get('value');
        $id = $request->request->get('pk');

        $field = $request->request->get('field');

        $object = $admin->getObject($id);

        /** @var \Sonata\DoctrineORMAdminBundle\Model\ModelManager $mm */
        $mm = $admin->getModelManager();

        $ClassMetadata = $mm->getMetadata($admin->getClass());

        $associationMapping = $ClassMetadata->getAssociationMapping($field);

        $items = $em->getRepository($associationMapping['targetEntity'])->findBy([
            'id' => $ids,
        ]);

        \call_user_func([$object, 'set' . ucfirst($field)], $items);

        $admin->update($object);

        $result = [
        ];

        $associationAdmin = $admin->getAdminByClass($associationMapping['targetEntity']);

        foreach ($items as $item) {
            if (method_exists($item, 'getId') && method_exists($item, 'getName')) {
                $result[] = [
                    'id' => $item->getId(),
                    'label' => $item->getName(),
                    'edit_url' => $associationAdmin->generateObjectUrl('edit', $item),
                ];
            }
        }

        return new JsonResponse([
            'items' => $result,
        ]);
    }

    /**
     * Обвновление связей ManyToOne.
     *
     * @param Request $request
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     *
     * @return JsonResponse
     */
    public function updateManyToOneAction(Request $request)
    {
        /** @var AbstractAdmin $admin */
        $admin = $this->getAdmin();

        $admin->checkAccess('edit');

        $em = $admin->getDoctrine()->getManager();

        $ids = $request->request->get('value');
        $id = $request->request->get('pk');
        $field = $request->request->get('field');

        $object = $admin->getObject($id);

        /** @var \Sonata\DoctrineORMAdminBundle\Model\ModelManager $mm */
        $mm = $admin->getModelManager();

        $ClassMetadata = $mm->getMetadata($admin->getClass());

        $associationMapping = $ClassMetadata->getAssociationMapping($field);

        $items = $em->getRepository($associationMapping['targetEntity'])->find($ids);

        \call_user_func([$object, 'set' . ucfirst($field)], $items);

        $admin->update($object);

        $result = [
        ];

        $associationAdmin = $admin->getAdminByClass($associationMapping['targetEntity']);

        $twigSonataAdminExtension = $admin->getContainer()->get('sonata.admin.twig.extension');

        $fieldDescription = $admin->getListFieldDescription($field);

        if ($items) {
            $result[] = [
                'id' => $items->getId(),
                'label' => $twigSonataAdminExtension->renderRelationElement($items, $fieldDescription),
                'edit_url' => $associationAdmin->generateObjectUrl('edit', $items),
            ];
        }

        return new JsonResponse([
            'items' => $result,
        ]);
    }
}
