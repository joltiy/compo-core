<?php

namespace Compo\ContactsBundle\Block;

use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Compo\ContactsBundle\Form\FeedbackFormType;

/**
 * {@inheritdoc}
 */
class FeedbackFormBlockService extends AbstractBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        $block = $blockContext->getBlock();
        $template = $blockContext->getTemplate();
        $formType = new FeedbackFormType();

        $container = $this->getContainer();
        $data = $container->get('form.factory')->create( $formType )->createView();
   

        return $this->renderResponse($template, array(
            'block' => $block,
            'settings' => $settings,
            'form' => $data
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', array(

        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildCreateForm(FormMapper $formMapper, BlockInterface $block)
    {
        $this->buildForm($formMapper, $block);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $this->buildForm($formMapper, $block);
    }


    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'template' => 'CompoContactsBundle:Form:feedback_form.html.twig',
        ));
    }

}
