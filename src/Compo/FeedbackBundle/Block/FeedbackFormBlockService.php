<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

        $form = $container->get('form.factory')->create($feedbackType['form'], null, ['type' => $type, 'extra_data' => $settings['extra_data']])->createView();

        return $this->renderResponse(
            $template,
            [
                'block' => $block,
                'settings' => $settings,
                'form' => $form,
            ],
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
            [
                'keys' => [
                    ['type', 'choice', ['choices' => $choices, 'label' => 'Тип формы']],
                    ['cssClass', 'text', ['label' => 'CSS Class', 'required' => false]],
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'cssClass' => '',
                'template' => '',
                'type' => '',
                'extra_data' => [],
            ]
        );
    }
}
