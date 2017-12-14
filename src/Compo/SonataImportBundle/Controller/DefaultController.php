<?php

namespace Compo\SonataImportBundle\Controller;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\SonataImportBundle\Entity\UploadFile;
use Compo\SonataImportBundle\Form\Type\UploadFileType;
use Compo\Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

class DefaultController extends CRUDController {

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request) {
        $fileEntity = new UploadFile();
        $form = $this->createForm(UploadFileType::class, $fileEntity, [
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if($form->isValid()){
            if(!$fileEntity->getFile()->getError()) {
                $fileEntity->move($this->getParameter('compo_sonata_import.upload_dir'));

                $this->getDoctrine()->getManager()->persist($fileEntity);
                $this->getDoctrine()->getManager()->flush($fileEntity);

                $this->runCommand($fileEntity);
                return $this->redirect($this->admin->generateUrl('upload', [
                    'id' => $fileEntity->getId()
                ]));
            } else {
                $form->get('file')->addError(new FormError($fileEntity->getFile()->getErrorMessage()));
            }
        }


        $builder = $this->get('sonata.admin.pool')
            ->getInstance($this->admin->getCode())
            ->getExportFields()
        ;
        return $this->render('@CompoSonataImport/Default/index.html.twig', [
            'form' => $form->createView(),
            'base_template' => $this->getBaseTemplate(),
            'builder' => $builder,
            'action' => 'import',
            'letters' => $this->getLetterArray()
        ]);
    }

    /**
     * @param Request    $request
     * @param UploadFile $uploadFile
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction(Request $request, UploadFile $uploadFile){
        $em = $this->getDoctrine()->getManager();

        $countImport = $em->getRepository('CompoSonataImportBundle:ImportLog')->count([
            'uploadFile' => $uploadFile->getId()
        ]);

        $data = $em->getRepository('CompoSonataImportBundle:ImportLog')->pagerfanta($request);

        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $data, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );


        return $this->render('@CompoSonataImport/Default/upload.html.twig', [
            'uploadFile' => $uploadFile,
            'paginator' => $pagination,
            'action' => 'upload',
            'admin' => $this->admin,
            'countImport' => $countImport,
            'baseTemplate' => $this->getBaseTemplate(),
        ]);
    }


    /**
     * @param UploadFile $uploadFile
     * @return JsonResponse
     */
    public function importStatusAction(UploadFile $uploadFile){
        $countImport = $this->getDoctrine()->getManager()->getRepository('CompoSonataImportBundle:ImportLog')->count([
            'uploadFile' => $uploadFile->getId()
        ]);

        return new JsonResponse([
            'status' => $uploadFile->getStatus(),
            'error' => $uploadFile->getMessage(),
            'count' => $countImport
        ]);
    }

    /**
     * get array from A to ZZ
     * @return array
     */
    private function getLetterArray(){
        $array = range('A', 'Z');
        $letters = $array;
        foreach($array as $first) {
            foreach ($array as $second) {
                $letters[] = $first . $second;
            }
        }
        return $letters;
    }

    /**
     * @param UploadFile $fileEntity
     */
    private function runCommand(UploadFile $fileEntity){
        $command = sprintf(
            '/usr/bin/php %s/console compo:sonata:import %d "%s" "%s" %d > /dev/null 2>&1 &',
            $this->get('kernel')->getRootDir() . '/../bin',
            $fileEntity->getId(),
            $this->admin->getCode(),
            $fileEntity->getEncode() ? $fileEntity->getEncode() : 'utf8',
            $fileEntity->getLoaderClass()
        );

        $process = new Process($command);
        $process->run();
    }
}
