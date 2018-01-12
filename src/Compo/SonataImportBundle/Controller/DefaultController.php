<?php

namespace Compo\SonataImportBundle\Controller;

use Compo\Sonata\AdminBundle\Controller\CRUDController;
use Compo\SonataImportBundle\Entity\UploadFile;
use Compo\SonataImportBundle\Form\Type\UploadFileType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

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
            if (!$fileEntity->getFile()->getError()) {
                $fileEntity->move($this->getParameter('compo_sonata_import.upload_dir'));

                $this->getDoctrine()->getManager()->persist($fileEntity);
                $this->getDoctrine()->getManager()->flush($fileEntity);

                $this->runCommand($fileEntity);

                return $this->redirect($this->admin->generateUrl('upload', [
                    'import_id' => $fileEntity->getId(),
                ]));
            }
            $form->get('file')->addError(new FormError($fileEntity->getFile()->getErrorMessage()));
        }

        $builder = $this->get('sonata.admin.pool')
            ->getInstance($this->admin->getCode())
            ->getExportFields();

        return $this->render('@CompoSonataImport/Default/index.html.twig', [
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
                '/usr/bin/php %s/console compo:sonata:import %d "%s" "%s" %d --dry-run > /dev/null 2>&1 &',
                $this->get('kernel')->getRootDir() . '/../bin',
                $fileEntity->getId(),
                $this->admin->getCode(),
                $fileEntity->getEncode() ? $fileEntity->getEncode() : 'utf8',
                $fileEntity->getLoaderClass()
            );
        } else {
            $command = sprintf(
                '/usr/bin/php %s/console compo:sonata:import %d "%s" "%s" %d > /dev/null 2>&1 &',
                $this->get('kernel')->getRootDir() . '/../bin',
                $fileEntity->getId(),
                $this->admin->getCode(),
                $fileEntity->getEncode() ? $fileEntity->getEncode() : 'utf8',
                $fileEntity->getLoaderClass()
            );
        }

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
     * @param Request    $request
     * @param UploadFile $uploadFile
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $uploadFile = $em->getRepository('CompoSonataImportBundle:UploadFile')->find($request->get('import_id'));

        $countImport = $em->getRepository('CompoSonataImportBundle:ImportLog')->count([
            'uploadFile' => $uploadFile->getId(),
        ]);

        $data = $em->getRepository('CompoSonataImportBundle:ImportLog')->pagerfanta($request);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            100
        );

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
            $stats[$statKey]['count'] = $em->getRepository('CompoSonataImportBundle:ImportLog')->count([
                'uploadFile' => $uploadFile->getId(),
                'status' => $stat['status'],
            ]);
        }

        return $this->render('@CompoSonataImport/Default/upload.html.twig', [
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
     * @param UploadFile $uploadFile
     *
     * @return JsonResponse
     */
    public function importStatusAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $uploadFile = $em->getRepository('CompoSonataImportBundle:UploadFile')->find($request->get('import_id'));

        $countImport = $this->getDoctrine()->getManager()->getRepository('CompoSonataImportBundle:ImportLog')->count([
            'uploadFile' => $uploadFile->getId(),
        ]);

        return new JsonResponse([
            'status' => $uploadFile->getStatus(),
            'error' => $uploadFile->getMessage(),
            'count' => $countImport,
        ]);
    }
}
