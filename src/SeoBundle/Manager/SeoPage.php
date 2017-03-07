<?php

namespace Compo\SeoBundle\Manager;


use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\SeoBundle\Service\BaseService;

class SeoPage extends \Sonata\SeoBundle\Seo\SeoPage
{

    use ContainerAwareTrait;


    public $services = array();

    public $context = 'default';

    /**
     * @var string
     */
    protected $linkNext;

    /**
     * @var string
     */
    protected $linkPrev;

    /**
     * @var array
     */
    protected $htmlAttributes = array();

    public $templates = array(
        'default' => array(
            'header' => '{{ page_internal.name }}',
            'description' => '{{ page_internal.name }}',
            'title' => '{{ page_internal.name }}. {{ site.title|default(site.name) }}',
            'meta_description' => '{{ page_internal.name }}. {{ site.metaDescription|default(site.name) }}',
            'meta_keyword' => '{{ page_internal.name }}, {{ site.metaKeyword|default(site.name) }}',
        )
    );


    public $vars = array();

    /**
     * @var string
     */
    protected $header;

    /**
     * @var string
     */
    protected $description;

    /**
     * @return string
     */
    public function getLinkNext()
    {
        return $this->linkNext;
    }

    /**
     * @param string $linkNext
     */
    public function setLinkNext($linkNext)
    {
        $this->linkNext = $linkNext;
    }

    /**
     * @return string
     */
    public function getLinkPrev()
    {
        return $this->linkPrev;
    }

    /**
     * @param string $linkPrev
     */
    public function setLinkPrev($linkPrev)
    {
        $this->linkPrev = $linkPrev;
    }


    /**
     * @param $service BaseService
     * @param $alias
     * @param $context
     */
    public function addService($service, $alias, $context) {
        $this->services[$alias] = $service;
        $service->setAlias($alias);
        $service->setContext($context);

        $service->setSeoPage($this);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    public function loadVars()
    {

    }

    public function addVar($name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * @param array $templates
     */
    public function setTemplates($templates)
    {
        $this->templates = $templates;
    }

    /**
     * @return array
     */
    public function getVars()
    {
        if ($this->getContainer()->get('request')->get('_route') == 'page_slug') {
            $this->setContext('page');
        }

        $this->vars['context'] = $this->getContext();

        return $this->vars;
    }

    public function getVar($name, $default = null) {
        if (isset($this->vars[$name])) {
            return $this->vars[$name];
        } else {
            return $default;
        }
    }

    /**
     * @param array $vars
     */
    public function setVars($vars)
    {
        $this->vars = $vars;
    }



    public function addTemplates($name, $templates) {
        $this->templates[$name] = $templates;
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
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param array $services
     */
    public function setServices($services)
    {
        $this->services = $services;
    }

    public function getSite() {
        $container = $this->getContainer();
        $site = $container->get('sonata.page.site.selector')->retrieve();

        return $site;
    }

    public function build() {

        $this->addHtmlAttributes('lang', 'ru');

        $container = $this->getContainer();

        $site = $container->get('sonata.page.site.selector')->retrieve();

        $this->addVar('site', $site);

        $context = $this->getContext();

        $request = $container->get('request');

        $route = $request->get('_route');

        foreach ($this->services as $service) {

            if ($service->handleContext($context) || $service->handleContext($route)) {
                $service->build();

                $serviceVars = $service->getVars();

                foreach ($serviceVars as $serviceVarsItem => $serviceVarsItem) {
                    $this->addVar($serviceVarsItem, $serviceVarsItem);
                }
            }
        }

        $templates = array_reverse($this->templates);


        $header = '';
        $description = '';

        $title = '';
        $meta_keyword = '';
        $meta_description = '';

        foreach ($templates as $template) {
            if (isset($template['header']) && $header == '') {
                $header = $this->buildTemplate($template['header']);
            }

            if (isset($template['description']) && $description == '') {
                $description = $this->buildTemplate($template['description']);
            }

            if (isset($template['title']) && $title == '') {
                $title = $this->buildTemplate($template['title']);
            }
            if (isset($template['meta_keyword']) && $meta_keyword == '') {
                $meta_keyword = $this->buildTemplate($template['meta_keyword']);
            }
            if (isset($template['meta_description']) && $meta_description == '') {
                $meta_description = $this->buildTemplate($template['meta_description']);
            }
        }

        // Чистка keywords
        $keywords_tmp = explode(',', $meta_keyword);

        foreach ($keywords_tmp as $key => $item) {
            $item = trim($item);
            $keywords_tmp[$key] = $item;
            if ($item == '') {
                unset($keywords_tmp[$key]);
            }
        }

        $header = trim($header);
        $description = trim($description);
        $title = trim($title);
        $meta_description = trim($meta_description);

        $meta_keyword = trim(implode(',', array_unique($keywords_tmp)));


        if ($header) {
            $this->setHeader($header);
        }

        if ($description) {
            $this->setDescription($description);
        }

        if ($title) {
            $this->setTitle($title);
        }

        if ($meta_description) {
            $this->addMeta('name', 'description', $meta_description);
        }

        if ($meta_keyword) {
            $this->addMeta('name', 'keywords', $meta_keyword);
        }
    }


    public function buildTemplate($template) {
        // Заменяем переменные в шаблоне
        //$found_template[$key] = preg_replace(array_keys($vars_preg_replace), array_values($vars_preg_replace), $value);

        try {
            $loader = new \Twig_Loader_Array(array(
                'index.html' => '{% spaceless %}' . $template . '{% endspaceless %}',
            ));
            $twig = new \Twig_Environment($loader, array(
                'autoescape' => false,
                'debug' => false,
            ));

            $result = $twig->render('index.html', $this->vars);
        } catch (\Exception $e) {
            $result = '';
        }

        // Вырезаем незамененые переменные
        $result = trim(preg_replace('/\{\{.*\}\}/im', ' ', $result));

        $result = str_replace('()', '', $result);
        $result = str_replace(' ,', ',', $result);
        $result = str_replace(' .', '.', $result);
        $result = str_replace('( )', '', $result);

        $result = trim($result, ',.:');

        // Удалить два и более пробела
        $result = trim(preg_replace('/ {2,}/im', ' ', $result));

        return $result;
    }
}