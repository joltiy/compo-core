<?php

namespace Compo\CoreBundle\Twig;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\Pool;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

/**
 * {@inheritDoc}
 */
class MediaExtension extends \Twig_Extension
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    protected $resources = array();

    /**
     * @var Pool
     */
    protected $mediaService;
    /**
     * @var ManagerInterface
     */
    protected $mediaManager;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @param ManagerInterface $mediaManager
     */
    public function __construct(Pool $mediaService, ManagerInterface $mediaManager, CacheManager $cacheManager, FilterManager $filterManager)
    {
        $this->mediaService = $mediaService;

        $this->mediaManager = $mediaManager;

        $this->cacheManager = $cacheManager;

        $this->filterManager = $filterManager;

    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('media_width', array($this, 'getWidth'), array('is_safe' => array('html'))),

            new \Twig_SimpleFunction('media_height', array($this, 'getHeight'), array('is_safe' => array('html'))),

            new \Twig_SimpleFunction('media_path', array($this, 'getPath'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('media_thumbnail', array($this, 'getThumbnail'), array('is_safe' => array('html'))),

        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'compo_media';
    }

    public function getPath($media, $options = array())
    {
        $format = 'reference';

        if (isset($options['format'])) {
            $format = $options['format'];
        }

        $media = $this->getMedia($media);

        if (!$media) {
            return '';
        }

        $provider = $this->getMediaService()
            ->getProvider($media->getProviderName());

        $format = $provider->getFormatName($media, $format);

        $publicUrl = $provider->generatePublicUrl($media, $format);

        if (isset($options['filter'])) {
            if (!isset($options['filter_options'])) {
                $options['filter_options'] = array();
            }

            return $this->cacheManager->getBrowserPath($publicUrl, $options['filter'], $options['filter_options']);
        }

        return $publicUrl;
    }

    public function getThumbnail($media, $options_filter = array(), $attr = array())
    {
        $format = 'reference';

        if (isset($options_filter['format'])) {
            $format = $options_filter['format'];
        }

        $media = $this->getMedia($media);

        if (!$media) {
            return '';
        }

        $provider = $this->getMediaService()
            ->getProvider($media->getProviderName());

        $format = $provider->getFormatName($media, $format);
        $format_definition = $provider->getFormat($format);

        // build option
        $defaultOptions = array(
            'title' => $media->getName(),
            'alt' => $media->getName(),
        );

        if ($format_definition['width']) {
            $defaultOptions['width'] = $format_definition['width'];
        }

        if ($format_definition['height']) {
            $defaultOptions['height'] = $format_definition['height'];
        }

        if (isset($options_filter['filter'])) {
            $runtimeConfig = array();

            if (isset($options_filter['filter_options'])) {
                $runtimeConfig = $options_filter['filter_options'];
            }

            $config = array_replace_recursive(
                $this->filterManager->getFilterConfiguration()->get($options_filter['filter']),
                $runtimeConfig
            );


            if (isset($config['filters'])) {
                foreach ($config['filters'] as $config_item) {
                    if (isset($config_item['size'])) {
                        if (isset($config_item['size'][0]) && $config_item['size'][0]) {
                            $defaultOptions['width'] = $config_item['size'][0];
                        }

                        if (isset($config_item['size'][1]) && $config_item['size'][1]) {
                            $defaultOptions['height'] = $config_item['size'][1];
                        }
                    }

                    if (isset($config_item['width'])) {
                        $defaultOptions['width'] = $config_item['width'];
                    }

                    if (isset($config_item['height'])) {
                        $defaultOptions['height'] = $config_item['height'];
                    }
                }
            }
        }


        $attr = array_merge($defaultOptions, $attr);

        if (isset($attr['width']) && !$attr['width']) {
            unset($attr['width']);
        }

        if (isset($attr['height']) && !$attr['height']) {
            unset($attr['height']);
        }

        $attr['src'] = $this->getPath($media, $options_filter);

        return $this->render($provider->getTemplate('helper_thumbnail'), array(
            'media' => $media,
            'options' => $attr,
        ));
    }

    /**
     * @param string $template
     * @param array  $parameters
     *
     * @return mixed
     */
    public function render($template, array $parameters = array())
    {
        if (!isset($this->resources[$template])) {
            $this->resources[$template] = $this->environment->loadTemplate($template);
        }

        return $this->resources[$template]->render($parameters);
    }

    /**
     * @return Pool
     */
    public function getMediaService()
    {
        return $this->mediaService;
    }

    /**
     * @param $media
     * @return string
     */
    public function getWidth($media)
    {
        $media = $this->getMedia($media);

        if ($media) {
            return $media->getWidth();
        }

        return null;
    }

    /**
     * @param mixed $media
     *
     * @return MediaInterface|null|bool
     */
    private function getMedia($media)
    {
        if (!$media instanceof MediaInterface && strlen($media) > 0) {
            $media = $this->mediaManager->findOneBy(array(
                'id' => $media,
            ));
        }

        if (!$media instanceof MediaInterface) {
            return false;
        }

        if ($media->getProviderStatus() !== MediaInterface::STATUS_OK) {
            return false;
        }

        return $media;
    }

    /**
     * @param $media
     * @return string
     */
    public function getHeight($media)
    {
        $media = $this->getMedia($media);

        if ($media) {
            return $media->getHeight();
        }

        return null;
    }
}
