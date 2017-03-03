<?php

namespace Compo\SeoBundle\Service;


use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\SeoBundle\Manager\SeoPage;

class BaseService
{
    use ContainerAwareTrait;


    public $context = array();
    public $alias = 'default';

    /** @var SeoPage */
    public $seoPage;

    /**
     * @return SeoPage
     */
    public function getSeoPage()
    {
        return $this->seoPage;
    }

    /**
     * @param mixed $seoPage
     */
    public function setSeoPage($seoPage)
    {
        $this->seoPage = $seoPage;
    }



    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function getVars() {
        return array();
    }

    public function build() {

    }

    /**
     * @param string $context
     *
     * @return bool
     */
    public function handleContext($context)
    {
        return $this->context === $context;
    }
}