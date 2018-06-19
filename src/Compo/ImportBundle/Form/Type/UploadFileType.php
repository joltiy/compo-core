<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ImportBundle\Form\Type;

use Compo\ImportBundle\Entity\UploadFile;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UploadFileType.
 */
class UploadFileType extends AbstractType
{
    use ContainerAwareTrait;

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'form.file',
            ])
        ;

        $default_encode = $this->container->getParameter('compo_import.encode.default');

        /** @var array $encode_list */
        $encode_list = $this->container->getParameter('compo_import.encode.list');

        if (!\count($encode_list)) {
            $builder->add('encode', HiddenType::class, [
                'data' => $default_encode,
                'label' => 'form.encode',
            ]);
        } else {
            $el = [];

            foreach ($encode_list as $item) {
                $el[$item] = $item;
            }

            $builder->add('encode', ChoiceType::class, [
                'choices' => $el,
                'data' => $default_encode,
                'label' => 'form.encode',
            ]);
        }

        $loader = [];

        /** @var array $loaders_list */
        $loaders_list = $this->container->getParameter('compo_import.class_loaders');

        foreach ($loaders_list as $key => $item) {
            $loader[$item['name']] = $key;
        }

        $builder->add('loaderClass', ChoiceType::class, [
            'choices' => $loader,
            'label' => 'form.loader_class',
        ]);

        $builder->add('dryRun', CheckboxType::class, [
            'required' => false,
            'label' => 'Пробный импорт',
        ]);

        $builder
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit',
                'attr' => [
                    'class' => 'btn btn-success',
                ],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UploadFile::class,
            'translation_domain' => 'CompoImportBundle',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'compo_sonataadminbundle_uploadfile';
    }
}
