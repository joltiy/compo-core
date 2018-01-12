<?php

namespace Compo\CoreBundle\Translation;

use Sylius\Bundle\ThemeBundle\Translation\Provider\Loader\TranslatorLoaderProviderInterface;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Resource\TranslatorResourceProviderInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator as BaseTranslator;

/**
 * Class FallbackTranslator.
 */
class ThemeTranslator extends BaseTranslator implements WarmableInterface
{
    /**
     * @var array
     */
    protected $options = [
        'cache_dir' => null,
        'debug' => false,
    ];

    /**
     * @var TranslatorLoaderProviderInterface
     */
    private $loaderProvider;

    /**
     * @var TranslatorResourceProviderInterface
     */
    private $resourceProvider;

    /**
     * @var bool
     */
    private $resourcesLoaded = false;

    /**
     * @param TranslatorLoaderProviderInterface   $loaderProvider
     * @param TranslatorResourceProviderInterface $resourceProvider
     * @param MessageSelector                     $messageSelector
     * @param string                              $locale
     * @param array                               $options
     */
    public function __construct(
        TranslatorLoaderProviderInterface $loaderProvider,
        TranslatorResourceProviderInterface $resourceProvider,
        MessageSelector $messageSelector,
        $locale,
        array $options = []
    ) {
        $this->assertOptionsAreKnown($options);

        $this->loaderProvider = $loaderProvider;
        $this->resourceProvider = $resourceProvider;

        $this->options = array_merge($this->options, $options);
        if (null !== $this->options['cache_dir'] && $this->options['debug']) {
            $this->addResources();
        }

        parent::__construct($locale, $messageSelector, $this->options['cache_dir'], $this->options['debug']);
    }

    /**
     * @param array $options
     */
    private function assertOptionsAreKnown(array $options)
    {
        if ($diff = array_diff(array_keys($options), array_keys($this->options))) {
            throw new \InvalidArgumentException(sprintf('The Translator does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }
    }

    private function addResources()
    {
        if ($this->resourcesLoaded) {
            return;
        }

        $resources = $this->resourceProvider->getResources();
        foreach ($resources as $resource) {
            $this->addResource(
                $resource->getFormat(),
                $resource->getName(),
                $resource->getLocale(),
                $resource->getDomain()
            );
        }

        $this->resourcesLoaded = true;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        // skip warmUp when translator doesn't use cache
        if (null === $this->options['cache_dir']) {
            return;
        }

        $locales = array_merge(
            $this->getFallbackLocales(),
            [$this->getLocale()],
            $this->resourceProvider->getResourcesLocales()
        );
        foreach (array_unique($locales) as $locale) {
            // reset catalogue in case it's already loaded during the dump of the other locales.
            if (isset($this->catalogues[$locale])) {
                unset($this->catalogues[$locale]);
            }

            $this->loadCatalogue($locale);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        if (null === $locale) {
            $locale = $this->getLocale();
        }

        if (null === $domain) {
            $domain = 'messages';
        }

        if (!isset($this->catalogues[$locale])) {
            $this->loadCatalogue($locale);
        }

        // Change translation domain to 'messages' if a translation can't be found in the
        // current domain
        if ('messages' !== $domain && false === $this->catalogues[$locale]->has((string) $id, $domain)) {
            $domain = 'messages';
        }

        return strtr($this->catalogues[$locale]->get((string) $id, $domain), $parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeCatalogue($locale)
    {
        $this->initialize();

        parent::initializeCatalogue($locale);
    }

    private function initialize()
    {
        $this->addResources();
        $this->addLoaders();
    }

    private function addLoaders()
    {
        $loaders = $this->loaderProvider->getLoaders();
        foreach ($loaders as $alias => $loader) {
            $this->addLoader($alias, $loader);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function computeFallbackLocales($locale)
    {
        $locales = parent::computeFallbackLocales($locale);

        while (false !== mb_strrchr($locale, '_')) {
            $locale = mb_substr($locale, 0, -mb_strlen(mb_strrchr($locale, '_')));

            array_unshift($locales, $locale);
        }

        return array_unique($locales);
    }
}
