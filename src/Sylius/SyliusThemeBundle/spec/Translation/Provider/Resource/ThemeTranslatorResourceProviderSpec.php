<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Translation\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Translation\Finder\TranslationFilesFinderInterface;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Resource\ThemeTranslatorResourceProvider;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Resource\TranslatorResourceProviderInterface;
use Sylius\Bundle\ThemeBundle\Translation\Resource\ThemeTranslationResource;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ThemeTranslatorResourceProviderSpec extends ObjectBehavior
{
    public function let(
        TranslationFilesFinderInterface $translationFilesFinder,
        ThemeRepositoryInterface $themeRepository,
        ThemeHierarchyProviderInterface $themeHierarchyProvider
    ) {
        $this->beConstructedWith($translationFilesFinder, $themeRepository, $themeHierarchyProvider);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ThemeTranslatorResourceProvider::class);
    }

    public function it_implements_translator_resource_provider_interface()
    {
        $this->shouldImplement(TranslatorResourceProviderInterface::class);
    }

    public function it_returns_translation_files_found_in_given_paths(
        TranslationFilesFinderInterface $translationFilesFinder,
        ThemeRepositoryInterface $themeRepository,
        ThemeHierarchyProviderInterface $themeHierarchyProvider,
        ThemeInterface $theme
    ) {
        $themeRepository->findAll()->willReturn(array($theme));
        $themeHierarchyProvider->getThemeHierarchy($theme)->willReturn(array($theme));

        $theme->getPath()->willReturn('/theme/path');
        $theme->getName()->willReturn('theme/name');

        $translationFilesFinder->findTranslationFiles('/theme/path')->willReturn(array('/theme/path/messages.en.yml'));

        $this->getResources()->shouldBeLike(array(
            new ThemeTranslationResource($theme->getWrappedObject(), '/theme/path/messages.en.yml'),
        ));
    }

    public function it_returns_inherited_themes_as_the_main_theme_resources(
        TranslationFilesFinderInterface $translationFilesFinder,
        ThemeRepositoryInterface $themeRepository,
        ThemeHierarchyProviderInterface $themeHierarchyProvider,
        ThemeInterface $mainTheme,
        ThemeInterface $parentTheme
    ) {
        $themeRepository->findAll()->willReturn(array($mainTheme));
        $themeHierarchyProvider->getThemeHierarchy($mainTheme)->willReturn(array($mainTheme, $parentTheme));

        $mainTheme->getPath()->willReturn('/main/theme/path');
        $mainTheme->getName()->willReturn('main-theme/name');

        $parentTheme->getPath()->willReturn('/parent/theme/path');
        $parentTheme->getName()->willReturn('parent-theme/name');

        $translationFilesFinder->findTranslationFiles('/main/theme/path')->willReturn(array('/main/theme/path/messages.en.yml'));
        $translationFilesFinder->findTranslationFiles('/parent/theme/path')->willReturn(array('/parent/theme/path/messages.en.yml'));

        $this->getResources()->shouldBeLike(array(
            new ThemeTranslationResource($mainTheme->getWrappedObject(), '/parent/theme/path/messages.en.yml'),
            new ThemeTranslationResource($mainTheme->getWrappedObject(), '/main/theme/path/messages.en.yml'),
        ));
    }

    public function it_doubles_resources_if_used_in_more_than_one_theme(
        TranslationFilesFinderInterface $translationFilesFinder,
        ThemeRepositoryInterface $themeRepository,
        ThemeHierarchyProviderInterface $themeHierarchyProvider,
        ThemeInterface $mainTheme,
        ThemeInterface $parentTheme
    ) {
        $themeRepository->findAll()->willReturn(array($mainTheme, $parentTheme));
        $themeHierarchyProvider->getThemeHierarchy($mainTheme)->willReturn(array($mainTheme, $parentTheme));
        $themeHierarchyProvider->getThemeHierarchy($parentTheme)->willReturn(array($parentTheme));

        $mainTheme->getPath()->willReturn('/main/theme/path');
        $mainTheme->getName()->willReturn('main-theme/name');

        $parentTheme->getPath()->willReturn('/parent/theme/path');
        $parentTheme->getName()->willReturn('parent-theme/name');

        $translationFilesFinder->findTranslationFiles('/main/theme/path')->willReturn(array('/main/theme/path/messages.en.yml'));
        $translationFilesFinder->findTranslationFiles('/parent/theme/path')->willReturn(array('/parent/theme/path/messages.en.yml'));

        $this->getResources()->shouldBeLike(array(
            new ThemeTranslationResource($mainTheme->getWrappedObject(), '/parent/theme/path/messages.en.yml'),
            new ThemeTranslationResource($mainTheme->getWrappedObject(), '/main/theme/path/messages.en.yml'),
            new ThemeTranslationResource($parentTheme->getWrappedObject(), '/parent/theme/path/messages.en.yml'),
        ));
    }

    public function it_returns_resources_locales_while_using_just_one_theme(
        TranslationFilesFinderInterface $translationFilesFinder,
        ThemeRepositoryInterface $themeRepository,
        ThemeHierarchyProviderInterface $themeHierarchyProvider,
        ThemeInterface $theme
    ) {
        $themeRepository->findAll()->willReturn(array($theme));
        $themeHierarchyProvider->getThemeHierarchy($theme)->willReturn(array($theme));

        $theme->getPath()->willReturn('/theme/path');
        $theme->getName()->willReturn('theme/name');

        $translationFilesFinder->findTranslationFiles('/theme/path')->willReturn(array('/theme/path/messages.en.yml'));

        $this->getResourcesLocales()->shouldReturn(array('en@theme-name'));
    }

    public function it_returns_resources_locales_while_using_one_nested_theme(
        TranslationFilesFinderInterface $translationFilesFinder,
        ThemeRepositoryInterface $themeRepository,
        ThemeHierarchyProviderInterface $themeHierarchyProvider,
        ThemeInterface $mainTheme,
        ThemeInterface $parentTheme
    ) {
        $themeRepository->findAll()->willReturn(array($mainTheme));
        $themeHierarchyProvider->getThemeHierarchy($mainTheme)->willReturn(array($mainTheme, $parentTheme));

        $mainTheme->getPath()->willReturn('/main/theme/path');
        $mainTheme->getName()->willReturn('main-theme/name');

        $parentTheme->getPath()->willReturn('/parent/theme/path');
        $parentTheme->getName()->willReturn('parent-theme/name');

        $translationFilesFinder->findTranslationFiles('/main/theme/path')->willReturn(array('/main/theme/path/messages.en.yml'));
        $translationFilesFinder->findTranslationFiles('/parent/theme/path')->willReturn(array('/parent/theme/path/messages.en.yml'));

        $this->getResourcesLocales()->shouldReturn(array('en@main-theme-name'));
    }

    public function it_returns_resources_locales_while_using_more_than_one_theme(
        TranslationFilesFinderInterface $translationFilesFinder,
        ThemeRepositoryInterface $themeRepository,
        ThemeHierarchyProviderInterface $themeHierarchyProvider,
        ThemeInterface $mainTheme,
        ThemeInterface $parentTheme
    ) {
        $themeRepository->findAll()->willReturn(array($mainTheme, $parentTheme));
        $themeHierarchyProvider->getThemeHierarchy($mainTheme)->willReturn(array($mainTheme, $parentTheme));
        $themeHierarchyProvider->getThemeHierarchy($parentTheme)->willReturn(array($parentTheme));

        $mainTheme->getPath()->willReturn('/main/theme/path');
        $mainTheme->getName()->willReturn('main-theme/name');

        $parentTheme->getPath()->willReturn('/parent/theme/path');
        $parentTheme->getName()->willReturn('parent-theme/name');

        $translationFilesFinder->findTranslationFiles('/main/theme/path')->willReturn(array('/main/theme/path/messages.en.yml'));
        $translationFilesFinder->findTranslationFiles('/parent/theme/path')->willReturn(array('/parent/theme/path/messages.en.yml'));

        $this->getResourcesLocales()->shouldReturn(array('en@main-theme-name', 'en@parent-theme-name'));
    }
}
