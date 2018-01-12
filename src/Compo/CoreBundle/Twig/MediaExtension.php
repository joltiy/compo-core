<?php

namespace Compo\CoreBundle\Twig;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\Pool;

/**
 * {@inheritdoc}
 */
class MediaExtension extends \Twig_Extension
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    protected $resources = [];

    /**
     * @var Pool
     */
    protected $mediaService;

    /**
     * @var ManagerInterface
     */
    protected $mediaManager;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @param Pool             $mediaService
     * @param ManagerInterface $mediaManager
     * @param CacheManager     $cacheManager
     * @param FilterManager    $filterManager
     */
    public function __construct(Pool $mediaService, ManagerInterface $mediaManager, CacheManager $cacheManager, FilterManager $filterManager)
    {
        $this->mediaService = $mediaService;

        $this->mediaManager = $mediaManager;

        $this->cacheManager = $cacheManager;

        $this->filterManager = $filterManager;
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('media_width', [$this, 'getWidth'], ['is_safe' => ['html']]),

            new \Twig_SimpleFunction('media_height', [$this, 'getHeight'], ['is_safe' => ['html']]),

            new \Twig_SimpleFunction('media_path', [$this, 'getPath'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('media_thumbnail', [$this, 'getThumbnail'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'compo_media';
    }

    /**
     * @param $media
     * @param array $options_filter
     * @param array $attr
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return mixed|string
     */
    public function getThumbnail($media, array $options_filter = [], array $attr = [])
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
        $defaultOptions = [
            'title' => $media->getName(),
            'alt' => $media->getName(),
        ];

        if ($format_definition['width']) {
            $defaultOptions['width'] = $format_definition['width'];
        }

        if ($format_definition['height']) {
            $defaultOptions['height'] = $format_definition['height'];
        }

        if (isset($options_filter['filter'])) {
            $runtimeConfig = [];

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

        return $this->render(
            $provider->getTemplate('helper_thumbnail'),
            [
                'media' => $media,
                'options' => $attr,
            ]
        );
    }

    /**
     * @param mixed $media
     *
     * @return MediaInterface|null|bool
     */
    private function getMedia($media)
    {
        if (!$media instanceof MediaInterface && mb_strlen($media) > 0) {
            $media = $this->mediaManager->findOneBy(
                [
                    'id' => $media,
                ]
            );
        }

        if (!$media instanceof MediaInterface) {
            return false;
        }

        if (MediaInterface::STATUS_OK !== $media->getProviderStatus()) {
            return false;
        }

        return $media;
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
     * @param array $options
     *
     * @return string
     */
    public function getPath($media, array $options = [])
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
                $options['filter_options'] = [];
            }

            return $this->cacheManager->getBrowserPath($publicUrl, $options['filter'], $options['filter_options']);
        }

        return $publicUrl;
    }

    /**
     * @param string $template
     * @param array  $parameters
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return mixed
     */
    public function render($template, array $parameters = [])
    {
        if (!isset($this->resources[$template])) {
            $this->resources[$template] = $this->environment->loadTemplate($template);
        }

        return $this->resources[$template]->render($parameters);
    }

    /**
     * @param $media
     *
     * @return int|null
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
     * @param $media
     *
     * @return int|null
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
