<?php

namespace Compo\FeedbackBundle\Block;

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


        $form = $container->get('form.factory')->create($feedbackType['form'], null, array('type' => $type, 'extra_data' => $settings['extra_data']))->createView();

        return $this->renderResponse(
            $template,
            array(
                'block' => $block,
                'settings' => $settings,
                'form' => $form
            ),
            $response
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormMapper $formMapper, BlockInterface $block)
    {

        $feedbackManager = $this->getContainer()->get('compo_feedback.manager.feedback');

        $choices = $feedbackManager->getTypesChoice();


        $formMapper->add(
            'settings',
            'sonata_type_immutable_array',
            array(
                'keys' => array(
                    array('type', 'choice', array('choices' => $choices, 'label' => 'Тип формы')),
                ),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'template' => '',
                'type' => '',
                'extra_data' => array()
            )
        );
    }
}
