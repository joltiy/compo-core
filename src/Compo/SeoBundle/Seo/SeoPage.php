<?php

namespace Compo\SeoBundle\Seo;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

/**
 * {@inheritDoc}
 */
class SeoPage extends \Sonata\SeoBundle\Seo\SeoPage
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    public $context = 'default';

    /**
     * @var bool
     */
    public $replaceTagging = true;

    /**
     * @var array
     */
    public $templates = array(
        'default' => array(
            'header' => '{{ page_internal.header|default(page_internal.name) }}',
            'description' => '{{ page_internal.description|default(page_internal.header)|default(page_internal.name) }}',
            'title' => '{{ page_internal.title|default(page_internal.header)|default(page_internal.name) }}',
            'metaDescription' => '{{ page_internal.metaDescription|default(page_internal.header)|default(page_internal.name) }}',
            'metaKeyword' => '{{ page_internal.metaKeyword|default(page_internal.header)|default(page_internal.name) }}',
        )
    );

    /**
     * @var array
     */
    public $vars = array();

    /**
     * @var array
     */
    protected $htmlAttributes = array();

    /**
     * @var string
     */
    protected $linkNext;

    /**
     * @var string
     */
    protected $linkPrev;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $header;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $descriptionAdditional;

    /**
     * @return bool
     */
    public function isReplaceTagging()
    {
        return $this->replaceTagging;
    }

    /**
     * @param bool $replaceTagging
     */
    public function setReplaceTagging($replaceTagging)
    {
        $this->replaceTagging = $replaceTagging;
    }

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
     * @return string
     */
    public function getDescriptionAdditional()
    {
        return $this->descriptionAdditional;
    }

    /**
     * @param string $descriptionAdditional
     */
    public function setDescriptionAdditional($descriptionAdditional)
    {
        $this->descriptionAdditional = $descriptionAdditional;
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
        if ($this->header) {
            return $this->header;
        }

        return $this->name;
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
        $route = $this->getRequest()->get('_route');

        if (
            $route === 'page_slug'
            ||
            $route === '_page_internal_error_not_found'
        ) {
            $this->setContext('page');
        }

        //current_uri
        $this->vars['current_uri'] = $this->getRequest()->getRequestUri();

        $this->vars['context'] = $this->getContext();

        return $this->vars;
    }

    /**
     * @param array $vars
     */
    public function setVars($vars)
    {
        $this->vars = $vars;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->getContainer()->get('request_stack')->getCurrentRequest();
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
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function getVar($name, $default = null)
    {
        if (isset($this->vars[$name])) {
            return $this->vars[$name];
        }

        return $default;
    }

    /**
     * @param $name
     * @param $templates
     */
    public function addTemplates($name, $templates)
    {
        $this->templates[$name] = $templates;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getTemplate($name)
    {
        if (isset($this->templates[$name])) {
            return $this->templates[$name];
        }

        return null;
    }

    /**
     * @return \Sonata\PageBundle\Model\SiteInterface
     */
    public function getSite()
    {
        return $this->getContainer()->get('sonata.page.site.selector')->retrieve();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @throws \Throwable
     */
    public function build()
    {
        $this->addHtmlAttributes('lang', 'ru');

        $container = $this->getContainer();

        $site = $container->get('sonata.page.site.selector')->retrieve();

        $this->addVar('site', $site);

        $templates = array_reverse($this->templates);

        /** @var \Compo\SeoBundle\Entity\SeoPage $contextTemplate */
        $contextTemplate = $container->get('compo_seo.page.manager')->findOneBy(array('context' => $this->context));

        if ($contextTemplate) {
            $templates[] = array(
                'header' => $contextTemplate->getHeader(),
                'title' => $contextTemplate->getTitle(),
                'metaKeyword' => $contextTemplate->getMetaKeyword(),
                'metaDescription' => $contextTemplate->getMetaDescription(),
            );
        }

        $name = '';

        $header = '';
        $description = '';
        $descriptionAdditional = '';

        $title = '';
        $meta_keyword = '';
        $meta_description = '';

        foreach ($templates as $template) {
            if ($name === '' && isset($template['name'])) {
                $name = $this->buildTemplate($template['name']);
            }

            if ($header === '' && isset($template['header'])) {
                $header = $this->buildTemplate($template['header']);
            }

            if ($description === '' && isset($template['description'])) {
                $description = $this->buildTemplate($template['description']);
            }

            if ($descriptionAdditional === '' && isset($template['descriptionAdditional'])) {
                $descriptionAdditional = $this->buildTemplate($template['descriptionAdditional']);
            }

            if ($title === '' && isset($template['title'])) {
                $title = $this->buildTemplate($template['title']);
            }

            if ($meta_keyword === '' && isset($template['metaKeyword'])) {
                $meta_keyword = $this->buildTemplate($template['metaKeyword']);
            }
            if ($meta_description === '' && isset($template['metaDescription'])) {
                $meta_description = $this->buildTemplate($template['metaDescription']);
            }
        }

        $title = $this->buildTemplate($title . ' {% if page|default("0") > 1 %} (страница {{ page }}){% endif %}');

        // Чистка keywords
        $keywords_tmp = explode(',', $meta_keyword);

        foreach ($keywords_tmp as $key => $item) {
            $item = trim($item);
            $keywords_tmp[$key] = $item;
            if ($item === '') {
                unset($keywords_tmp[$key]);
            }
        }

        $name = trim($name);
        $header = trim($header);
        $description = trim($description);
        $descriptionAdditional = trim($descriptionAdditional);
        $title = trim($title);
        $meta_description = trim($meta_description);

        $meta_keyword = trim(implode(',', array_unique($keywords_tmp)));

        if ($name) {
            $this->setName($name);
        }

        if ($header) {
            $this->setHeader($header);
        }

        if ($description) {
            $this->setDescription($description);
        }

        if ($descriptionAdditional) {
            $this->setDescriptionAdditional($descriptionAdditional);
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


    /**
     * @param $name
     * @param $value
     */
    public function addVar($name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * @param $template
     * @param array $vars
     * @return mixed|string
     * @throws \Throwable
     */
    public function buildTemplate($template, array $vars = array())
    {
        // Заменяем переменные в шаблоне
        //$found_template[$key] = preg_replace(array_keys($vars_preg_replace), array_values($vars_preg_replace), $value);

        try {
            //$tmp = $this->getContainer()->get('twig')->createTemplate('{% spaceless %} ' . $template . ' {% endspaceless %}');
            //                     'cache' => new \Twig_Cache_Filesystem($this->getConnector()->getTempDir(), \Twig_Cache_Filesystem::FORCE_BYTECODE_INVALIDATION),
            $tmp = $this->getContainer()->get('twig')->createTemplate('{% spaceless %} ' . $template . ' {% endspaceless %}');

            if ($vars) {
                $result = $tmp->render($vars);

            } else {
                $result = $tmp->render($this->vars);

            }

            /*
            $loader = new \Twig_Loader_Array(array(
                'index.html' => '{% spaceless %}' . $template . '{% endspaceless %}',
            ));

            $twig = new \Twig_Environment($loader, array(
                'autoescape' => false,
                'debug' => false
            ));

            $result = $twig->render('index.html', $this->vars);
            */

        } catch (\Exception $e) {
            $result = '';
        }

        $result = str_replace(' x ', ' ', $result);
        $result = str_replace(' х ', ' ', $result);


        // Вырезаем незамененые переменные
        //$result = trim(preg_replace('/\{\{.*\}\}/im', ' ', $result));

        $result = str_replace('()', '', $result);
        $result = str_replace(' ,', ',', $result);
        $result = str_replace(' .', '.', $result);
        $result = str_replace('( )', '', $result);

        $result = trim($result, ',:');

        // Удалить два и более пробела
        $result = trim(preg_replace('/ {2,}/m', ' ', $result));

        return $result;
    }
}
