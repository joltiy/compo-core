<?php

namespace Compo\SeoBundle\Seo;


use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

/**
 * {@inheritDoc}
 */
class SeoPage extends \Sonata\SeoBundle\Seo\SeoPage
{
    use ContainerAwareTrait;

    public $context = 'default';
    public $replaceTagging = true;

    public $templates = array(

        'default' => array(
            'header' => '{{ page_internal.header|default(page_internal.name) }}',
            'description' => '{{ page_internal.header|default(page_internal.name) }}',
            'title' => '{{ page_internal.header|default(page_internal.name) }}',
            'meta_description' => '{{ page_internal.header|default(page_internal.name) }}',
            'meta_keyword' => '{{ page_internal.header|default(page_internal.name) }}',
        )

    );

    public $vars = array();

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
        } else {
            return $this->name;
        }
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
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->getContainer()->get('request_stack')->getCurrentRequest();
    }

    /**
     * @return array
     */
    public function getVars()
    {
        if ($this->getRequest()->get('_route') == 'page_slug') {
            $this->setContext('page');
        }

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

    public function getVar($name, $default = null)
    {
        if (isset($this->vars[$name])) {
            return $this->vars[$name];
        } else {
            return $default;
        }
    }

    public function addTemplates($name, $templates)
    {
        $this->templates[$name] = $templates;
    }

    public function getTemplate($name)
    {
        if (isset($this->templates[$name])) {
            return $this->templates[$name];
        } else {
            return null;
        }
    }

    public function getSite()
    {
        $container = $this->getContainer();
        $site = $container->get('sonata.page.site.selector')->retrieve();

        return $site;
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
            if (isset($template['name']) && $header == '') {
                $name = $this->buildTemplate($template['name']);
            }

            if (isset($template['header']) && $header == '') {
                $header = $this->buildTemplate($template['header']);
            }

            if (isset($template['description']) && $description == '') {
                $description = $this->buildTemplate($template['description']);
            }

            if (isset($template['descriptionAdditional']) && $descriptionAdditional == '') {
                $descriptionAdditional = $this->buildTemplate($template['descriptionAdditional']);
            }

            if (isset($template['title']) && $title == '') {
                $title = $this->buildTemplate($template['title'] );
            }

            if (isset($template['metaKeyword']) && $meta_keyword == '') {
                $meta_keyword = $this->buildTemplate($template['metaKeyword']);
            }
            if (isset($template['metaDescription']) && $meta_description == '') {
                $meta_description = $this->buildTemplate($template['metaDescription']);
            }
        }

        $title = $this->buildTemplate($title . ' {% if page|default("0") > 1 %} страница ({{ page }}){% endif %}' );



        // Чистка keywords
        $keywords_tmp = explode(',', $meta_keyword);

        foreach ($keywords_tmp as $key => $item) {
            $item = trim($item);
            $keywords_tmp[$key] = $item;
            if ($item == '') {
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





    public function addVar($name, $value)
    {
        $this->vars[$name] = $value;
    }

    public function buildTemplate($template)
    {
        // Заменяем переменные в шаблоне
        //$found_template[$key] = preg_replace(array_keys($vars_preg_replace), array_values($vars_preg_replace), $value);

        try {
            $tmp = $this->getContainer()->get('twig')->createTemplate('{% spaceless %}' . $template . '{% endspaceless %}');
            //                     'cache' => new \Twig_Cache_Filesystem($this->getConnector()->getTempDir(), \Twig_Cache_Filesystem::FORCE_BYTECODE_INVALIDATION),

            $result = $tmp->render($this->vars);

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

        // Вырезаем незамененые переменные
        $result = trim(preg_replace('/\{\{.*\}\}/im', ' ', $result));

        $result = str_replace('()', '', $result);
        $result = str_replace(' ,', ',', $result);
        $result = str_replace(' .', '.', $result);
        $result = str_replace('( )', '', $result);

        $result = trim($result, ',:');

        // Удалить два и более пробела
        $result = trim(preg_replace('/ {2,}/im', ' ', $result));

        return $result;
    }
}