<?php

namespace Compo\Sonata\FormatterBundle\Block;


use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritDoc}
 */
class FormatterBlockService extends AbstractAdminBlockService
{
    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'format' => 'richhtml',
                'rawContent' => '',
                'content' => '',
                'template' => 'SonataFormatterBundle:Block:block_formatter.html.twig',
            )
        );
    }


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        return $this->renderResponse(
            $blockContext->getTemplate(),
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
            ),
            $response
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add(
            'settings',
            'sonata_type_immutable_array',
            array(
                'keys' => array(
                    array(
                        'content',
                        'sonata_formatter_type',
                        function (FormBuilderInterface $formBuilder) {
                            return array(
                                'event_dispatcher' => $formBuilder->getEventDispatcher(),
                                'format_field' => array('format', '[format]'),
                                'source_field' => array('rawContent', '[rawContent]'),
                                'target_field' => '[content]',
                                'label' => 'form.label_content',
                            );
                        }
                    ),
                ),
                'translation_domain' => 'SonataFormatterBundle',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata(
            $this->getName(), (null !== $code ? $code : $this->getName()), false, 'SonataFormatterBundle', array(
            'class' => 'fa fa-file-text-o',
        )
        );
    }
}