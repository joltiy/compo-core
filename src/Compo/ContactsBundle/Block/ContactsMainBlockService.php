<?php

namespace Compo\ContactsBundle\Block;

use Compo\Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Compo\ContactsBundle\Manager\ContactsManager;

/**
 * {@inheritdoc}
 */
class ContactsMainBlockService extends AbstractBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        $block = $blockContext->getBlock();
        $template = $blockContext->getTemplate();



        return $this->renderResponse($template, array(
            'block' => $block,
            'settings' => $settings,
        ), $response);
    }


    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $container = $this->getContainer();

        $manager = $container->get("compo_contacts.manager.contacts");
        $contacts = $manager->getContacts();

        $resolver->setDefaults(array(
            'template' => 'CompoContactsBundle:Block:contacts_main.html.twig',
            'contacts' => $contacts[0]
        ));
    }


}
