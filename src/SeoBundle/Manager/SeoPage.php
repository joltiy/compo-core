<?php

namespace Compo\SeoBundle\Manager;


use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

class SeoPage extends \Sonata\SeoBundle\Seo\SeoPage
{

    use ContainerAwareTrait;


    /**
     * @var array
     */
    protected $htmlAttributes = array();

    public $templates = array();


    public $vars = array();

    /**
     * @var string
     */
    protected $header;

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
        $container = $this->getContainer();

        $sites = $this->getContainer()->get('sonata.page.manager.site')->findAll();

        foreach ($sites as $site) {
            /** @var $site Site */
            $this->vars['site'] = $site;
        }
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
        return $this->vars;
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


    public function build() {
        $templates = array_reverse($this->templates);

        $header = '';
        $title = '';
        $meta_keyword = '';
        $meta_description = '';

        foreach ($templates as $template) {
            if (isset($template['header']) && $header == '') {
                $header = $this->buildTemplate($template['header']);
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
        $title = trim($title);
        $meta_description = trim($meta_description);

        $meta_keyword = trim(implode(',', array_unique($keywords_tmp)));

        if ($header) {
            $this->setHeader($header);
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

        // Удалить два и более пробела
        $result = trim(preg_replace('/ {2,}/im', ' ', $result));

        return $result;
    }
}