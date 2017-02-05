<?php

namespace Compo\CoreBundle\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\ExtractorInterface;
use Psr\Log\LoggerInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Security\Handler\SecurityHandlerInterface;
use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AdminExtractor
 *
 * @package Compo\CoreBundle\Translation
 */
class AdminExtractor implements ExtractorInterface, TranslatorInterface, SecurityHandlerInterface, LabelTranslatorStrategyInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Pool
     */
    private $adminPool;

    /**
     * @var string|bool|\Symfony\Component\Translation\MessageCatalogue
     */
    private $catalogue;

    /**
     * @var string|bool|TranslatorInterface
     */
    private $translator;

    /**
     * @var string|bool|LabelTranslatorStrategyInterface
     */
    private $labelStrategy;

    /**
     * @var string|bool
     */
    private $domain;

    /**
     * @param Pool $adminPool
     * @param LoggerInterface $logger
     */
    public function __construct(Pool $adminPool, LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        $this->adminPool = $adminPool;

        // state variable
        $this->catalogue = false;
        $this->translator = false;
        $this->labelStrategy = false;
        $this->domain = false;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Extract messages to MessageCatalogue.
     *
     * @return MessageCatalogue
     *
     * @throws \Exception|\RuntimeException
     */
    public function extract()
    {
        if ($this->catalogue) {
            //throw new \RuntimeException('Invalid state');
        }

        $this->catalogue = new MessageCatalogue();

        foreach ($this->adminPool->getAdminServiceIds() as $id) {
            $admin = $this->getAdmin($id);

            $this->translator = $admin->getTranslator();
            $this->labelStrategy = $admin->getLabelTranslatorStrategy();
            $this->domain = $admin->getTranslationDomain();

            $admin->setTranslator($this);
            $admin->setSecurityHandler($this);
            $admin->setLabelTranslatorStrategy($this);

//            foreach ($admin->getChildren() as $child) {
//                $child->setTranslator($this);
//            }

            // call the different public method
            $methods = array(
                'getShow' => array(array()),
                'getDatagrid' => array(array()),
                'getList' => array(array()),
                'getForm' => array(array()),
                'getBreadcrumbs' => array(
                    array('list'),
                    array('edit'),
                    array('create'),
                    array('update'),
                    array('batch'),
                    array('delete'),
                ),
            );

            if ($this->logger) {
                $this->logger->info(sprintf('Retrieving message from admin:%s - class: %s', $admin->getCode(), get_class($admin)));
            }

            foreach ($methods as $method => $calls) {
                foreach ($calls as $args) {
                    try {
                        call_user_func_array(array($admin, $method), $args);
                    } catch (\Exception $e) {
                        if ($this->logger) {
                            $this->logger->error(sprintf('ERROR : admin:%s - Raise an exception : %s', $admin->getCode(), $e->getMessage()));
                        }

                        //throw $e;
                    }
                }
            }
        }

        $catalogue = $this->catalogue;
        $this->catalogue = false;

        return $catalogue;
    }

    /**
     * @param string $id
     *
     * @return object|AdminInterface
     */
    private function getAdmin($id)
    {
        return $this->adminPool->getContainer()->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        $this->addMessage($id, $domain);

        return $id;
    }

    /**
     * @param string $id
     * @param string $domain
     */
    private function addMessage($id, $domain)
    {
        $message = new Message($id, $domain);

        //        $this->logger->debug(sprintf('extract: %s - domain:%s', $id, $domain));

        $trace = debug_backtrace(false);
        if (isset($trace[1]['file'])) {
            $message->addSource(new FileSource($trace[1]['file']));
        }


        $result = $this->adminPool->getContainer()->get('translator')->getCatalogue()->has($id);

        if ($result) {
            return;
        }


        $this->catalogue->add($message);
    }

    /**
     * {@inheritdoc}
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        $this->addMessage($id, $domain);

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->translator->setLocale($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted(AdminInterface $admin, $attributes, $object = null)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function buildSecurityInformation(AdminInterface $admin)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createObjectSecurity(AdminInterface $admin, $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function deleteObjectSecurity(AdminInterface $admin, $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseRole(AdminInterface $admin)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel($label, $context = '', $type = '')
    {
        $label = $this->labelStrategy->getLabel($label, $context, $type);

        $this->addMessage($label, $this->domain);

        return $label;
    }
}