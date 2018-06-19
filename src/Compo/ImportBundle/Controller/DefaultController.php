<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ImportBundle\Controller;

use Compo\ImportBundle\Entity\UploadFile;
use Compo\ImportBundle\Form\Type\UploadFileType;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Controller\CRUDController;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

/**
 * Class DefaultController.
 */
class DefaultController extends CRUDController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $fileEntity = new UploadFile();

        $form = $this->createForm(UploadFileType::class, $fileEntity, [
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /* @noinspection PhpUndefinedMethodInspection */
            if (!$fileEntity->getFile()->getError()) {
                $fileEntity->move($this->getParameter('kernel.root_dir') . '/../' . $this->getParameter('compo_import.upload_dir'));

                $this->getDoctrine()->getManager()->persist($fileEntity);
                $this->getDoctrine()->getManager()->flush();

                $this->runCommand($fileEntity);

                $this->getDoctrine()->getManager()->persist($fileEntity);
                $this->getDoctrine()->getManager()->flush();

                return $this->redirect($this->admin->generateUrl('upload', [
                    'import_id' => $fileEntity->getId(),
                ]));
            }

            /* @noinspection PhpUndefinedMethodInspection */
            $form->get('file')->addError(new FormError($fileEntity->getFile()->getErrorMessage()));
        }

        /** @var AbstractAdmin $admin */
        $admin = $this->get('sonata.admin.pool')->getInstance($this->admin->getCode());

        $builder = $admin->getExportFields();

        return $this->renderWithExtraParams('@CompoImport/Default/index.html.twig', [
            'form' => $form->createView(),
            'base_template' => $this->getBaseTemplate(),
            'builder' => $builder,
            'action' => 'import',
            'letters' => $this->getLetterArray(),
        ]);
    }

    /**
     * @param UploadFile $fileEntity
     */
    private function runCommand(UploadFile $fileEntity)
    {
        if ($fileEntity->isDryRun()) {
            $command = sprintf(
                '/usr/bin/php %s/console compo:import %d "%s" "%s" %d --dry-run > /dev/null 2>&1 &',
                $this->get('kernel')->getRootDir() . '/../bin',
                $fileEntity->getId(),
                $this->admin->getCode(),
                $fileEntity->getEncode() ?: 'utf8',
                $fileEntity->getLoaderClass()
            );
        } else {
            $command = sprintf(
                '/usr/bin/php %s/console compo:import %d "%s" "%s" %d > /dev/null 2>&1 &',
                $this->get('kernel')->getRootDir() . '/../bin',
                $fileEntity->getId(),
                $this->admin->getCode(),
                $fileEntity->getEncode() ?: 'utf8',
                $fileEntity->getLoaderClass()
            );
        }

        $fileEntity->setCommand($command);

        $process = new Process($command);
        $process->run();
    }

    /**
     * get array from A to ZZ.
     *
     * @return array
     */
    private function getLetterArray()
    {
        $array = range('A', 'Z');
        $letters = $array;
        foreach ($array as $first) {
            foreach ($array as $second) {
                $letters[] = $first . $second;
            }
        }

        return $letters;
    }

    /**
     * @param Request $request
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $uploadFile = $em->getRepository('CompoImportBundle:UploadFile')->find($request->get('import_id'));

        $countImport = $em->getRepository('CompoImportBundle:ImportLog')->count([
            'uploadFile' => $uploadFile->getId(),
        ]);

        $data = $em->getRepository('CompoImportBundle:ImportLog')->pagerfanta($request);

        $paginator = $this->get('knp_paginator');

        /** @var SlidingPagination $pagination */
        $pagination = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            100
        );

        $pagination->setCustomParameters(['type' => $request->get('type', 'all')]);

        $pagination->setParam('type', $request->get('type', 'all'));

        $stats = [
            0 => [
                'status' => 0,
                'count' => 0,
            ],
            1 => [
                'status' => 1,
                'count' => 0,
            ],
            2 => [
                'status' => 2,
                'count' => 0,
            ],
            3 => [
                'status' => 3,
                'count' => 0,
            ],
        ];

        foreach ($stats as $statKey => $stat) {
            $stats[$statKey]['count'] = $em->getRepository('CompoImportBundle:ImportLog')->count([
                'uploadFile' => $uploadFile->getId(),
                'status' => $stat['status'],
            ]);
        }

        return $this->renderWithExtraParams('@CompoImport/Default/upload.html.twig', [
            'uploadFile' => $uploadFile,
            'paginator' => $pagination,
            'action' => 'upload',
            'admin' => $this->admin,
            'stats' => $stats,

            'countImport' => $countImport,
            'base_template' => $this->getBaseTemplate(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return JsonResponse
     */
    public function importStatusAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $uploadFile = $em->getRepository('CompoImportBundle:UploadFile')->find($request->get('import_id'));

        $countImport = $this->getDoctrine()->getManager()->getRepository('CompoImportBundle:ImportLog')->count([
            'uploadFile' => $uploadFile->getId(),
        ]);

        return new JsonResponse([
            'status' => $uploadFile->getStatus(),
            'error' => $uploadFile->getMessage(),
            'count' => $countImport,
        ]);
    }
}
