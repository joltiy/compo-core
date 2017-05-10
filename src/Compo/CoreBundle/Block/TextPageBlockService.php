<?php

namespace Compo\CoreBundle\Block;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\FormatterBundle\Block\FormatterBlockService;

/**
 * {@inheritdoc}
 */
class TextPageBlockService extends FormatterBlockService
{
    use ContainerAwareTrait;




    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'format' => 'richhtml',
            'rawContent' => '<b>Текстовая страница с форматированием</b>',
            'content' => '<b>Текстовая страница с форматированием</b>',
            'template' => 'CompoCoreBundle:Block:text_page.html.twig',
        ));
    }
}
