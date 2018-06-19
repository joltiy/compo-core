<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Templating\Cache\Warmer;

use Doctrine\Common\Cache\Cache;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Templating\Locator\TemplateLocatorInterface;
use Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class TemplatePathsCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var TemplateFinderInterface
     */
    private $templateFinder;

    /**
     * @var TemplateLocatorInterface
     */
    private $templateLocator;

    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var Cache
     */
    private $cache;

    private $symonyLocator;

    private $symonyFinder;

    /**
     * @param TemplateFinderInterface  $templateFinder
     * @param TemplateLocatorInterface $templateLocator
     * @param ThemeRepositoryInterface $themeRepository
     * @param Cache                    $cache
     */
    public function __construct(
        TemplateFinderInterface $templateFinder,
        TemplateLocatorInterface $templateLocator,
        ThemeRepositoryInterface $themeRepository,
        Cache $cache,
        $symonyFinder,
        $symonyLocator
    ) {
        $this->templateFinder = $templateFinder;
        $this->templateLocator = $templateLocator;
        $this->themeRepository = $themeRepository;
        $this->cache = $cache;
        $this->symonyFinder = $symonyFinder;
        $this->symonyLocator = $symonyLocator;
    }

    protected function writeCacheFile($file, $content)
    {
        $tmpFile = @tempnam(dirname($file), basename($file));
        if (false !== @file_put_contents($tmpFile, $content) && @rename($tmpFile, $file)) {
            @chmod($file, 0666 & ~umask());

            return;
        }

        throw new \RuntimeException(sprintf('Failed to write cache file "%s".', $file));
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $filesystem = new Filesystem();
        $templatesFinal = [];

        foreach ($this->symonyFinder->findAllTemplates() as $template) {
            $templatesFinal[$template->getLogicalName()] = rtrim($filesystem->makePathRelative($this->symonyLocator->locate($template), $cacheDir), '/');
        }

        $templates = $this->templateFinder->findAllTemplates();

        /** @var TemplateReferenceInterface $template */
        foreach ($templates as $template) {
            $templateTemp = $this->warmUpTemplate($template);

            foreach ($templateTemp as $item => $value) {
                $templatesFinal[$item] = rtrim($filesystem->makePathRelative($value, $cacheDir), '/');
            }
        }

        $templatesStr = str_replace("' => '", "' => __DIR__.'/", var_export($templatesFinal, true));

        $this->writeCacheFile($cacheDir . '/templates.php', sprintf("<?php return %s;\n", $templatesStr));
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * @param TemplateReferenceInterface $template
     */
    private function warmUpTemplate(TemplateReferenceInterface $template)
    {
        $templateTemp = [];

        /** @var ThemeInterface $theme */
        foreach ($this->themeRepository->findAll() as $theme) {
            $templateTempItem = $this->warmUpThemeTemplate($template, $theme);

            if ($templateTempItem) {
                $templateTemp[$template->getLogicalName()] = rtrim($templateTempItem, '/');
            }
        }

        return $templateTemp;
    }

    /**
     * @param TemplateReferenceInterface $template
     * @param ThemeInterface             $theme
     */
    private function warmUpThemeTemplate(TemplateReferenceInterface $template, ThemeInterface $theme)
    {
        try {
            $location = $this->templateLocator->locateTemplate($template, $theme);
        } catch (ResourceNotFoundException $exception) {
            $location = null;
        }

        $this->cache->save($this->getCacheKey($template, $theme), $location);

        return $location;
    }

    /**
     * @param TemplateReferenceInterface $template
     * @param ThemeInterface             $theme
     *
     * @return string
     */
    private function getCacheKey(TemplateReferenceInterface $template, ThemeInterface $theme)
    {
        return $template->getLogicalName() . '|' . $theme->getName();
    }
}
