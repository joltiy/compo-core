<?php

namespace Compo\FeedbackBundle\Block;

use Compo\FeedbackBundle\Form\FeedbackBaseFormType;
use Compo\FeedbackBundle\Form\FeedbackFormType;
use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $container = $this->getContainer();

        $settings = $blockContext->getSettings();
        $block = $blockContext->getBlock();

        $template = $settings['template'];
        $type = $settings['type'];


        $feedbackManager = $container->get('compo_feedback.manager.feedback');

        $feedbackType = $feedbackManager->getType($type);

        if (!$template) {
            $template = $feedbackType['template'];
        }


        $data = $container->get('form.factory')->create($feedbackType['form'], null, array('type' => $type))->createView();

        return $this->renderResponse($template, array(
            'block' => $block,
            'settings' => $settings,
            'form' => $data
        ), $response);
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
    public function buildForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', array());
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
            'template' => '',
            'type' => '',

        ));
    }
}
