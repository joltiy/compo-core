<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Kernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class AppKernel.
 */
class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new \Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),

            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),

            new \Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),

            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new \Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new \Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),

            new \Craue\FormFlowBundle\CraueFormFlowBundle(),

            new \FOS\UserBundle\FOSUserBundle(),
            new \FOS\RestBundle\FOSRestBundle(),
            new \FOS\ElasticaBundle\FOSElasticaBundle(),
            new \FOS\JsRoutingBundle\FOSJsRoutingBundle(),

            new \Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new \SimpleThings\EntityAudit\SimpleThingsEntityAuditBundle(),
            new \Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new \Spy\TimelineBundle\SpyTimelineBundle(),
            new \Yectep\PhpSpreadsheetBundle\PhpSpreadsheetBundle(),
            new \WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new \winzou\Bundle\StateMachineBundle\winzouStateMachineBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new \Pix\SortableBehaviorBundle\PixSortableBehaviorBundle(),
            new \Presta\SitemapBundle\PrestaSitemapBundle(),
            new \KonstantinKuklin\AsseticStaticGzipBundle\AsseticStaticGzipBundle(),
            new \Debril\RssAtomBundle\DebrilRssAtomBundle(),
            new \Exporter\Bridge\Symfony\Bundle\SonataExporterBundle(),
            new \Evence\Bundle\SoftDeleteableExtensionBundle\EvenceSoftDeleteableExtensionBundle(),

            new \Liip\ImagineBundle\LiipImagineBundle(),
            new \Liip\MonitorBundle\LiipMonitorBundle(),

            new \Sylius\Bundle\SettingsBundle\SyliusSettingsBundle(),
            new \Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new \Sylius\Bundle\ThemeBundle\SyliusThemeBundle(),

            new \Sonata\UserBundle\SonataUserBundle(),
            new \Sonata\PageBundle\SonataPageBundle(),
            new \Sonata\MediaBundle\SonataMediaBundle(),
            new \Sonata\AdminBundle\SonataAdminBundle(),
            new \Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new \Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new \Sonata\CoreBundle\SonataCoreBundle(),
            new \Sonata\IntlBundle\SonataIntlBundle(),
            new \Sonata\FormatterBundle\SonataFormatterBundle(),
            new \Sonata\CacheBundle\SonataCacheBundle(),
            new \Sonata\BlockBundle\SonataBlockBundle(),
            new \Sonata\SeoBundle\SonataSeoBundle(),
            new \Sonata\NotificationBundle\SonataNotificationBundle(),
            new \Sonata\DatagridBundle\SonataDatagridBundle(),
            new \Sonata\TimelineBundle\SonataTimelineBundle(),
            new \Sonata\DashboardBundle\SonataDashboardBundle(),

            new \Compo\Sonata\AdminBundle\CompoSonataAdminBundle(),
            new \Compo\Sonata\CoreBundle\CompoSonataCoreBundle(),
            new \Compo\Sonata\MediaBundle\CompoSonataMediaBundle(),
            new \Compo\Sonata\DashboardBundle\CompoSonataDashboardBundle(),
            new \Compo\Sonata\PageBundle\CompoSonataPageBundle(),
            new \Compo\Sonata\TimelineBundle\CompoSonataTimelineBundle(),
            new \Compo\Sonata\UserBundle\CompoSonataUserBundle(),
            new \Compo\Sonata\BlockBundle\CompoSonataBlockBundle(),
            new \Compo\Sonata\NotificationBundle\CompoSonataNotificationBundle(),

            new \Compo\Sonata\FormatterBundle\CompoSonataFormatterBundle(),
            new \Compo\Sonata\SeoBundle\CompoSonataSeoBundle(),

            new \Compo\CoreBundle\CompoCoreBundle(),
            new \Compo\ImportBundle\CompoImportBundle(),

            new \Compo\SeoBundle\CompoSeoBundle(),
            new \Compo\MenuBundle\CompoMenuBundle(),
            new \Compo\NewsBundle\CompoNewsBundle(),
            new \Compo\ArticlesBundle\CompoArticlesBundle(),
            new \Compo\RedirectBundle\CompoRedirectBundle(),
            new \Compo\BannerBundle\CompoBannerBundle(),
            new \Compo\AdvantagesBundle\CompoAdvantagesBundle(),
            new \Compo\ContactsBundle\CompoContactsBundle(),
            new \Compo\PageCodeBundle\CompoPageCodeBundle(),

            new \Compo\EcommerceBundle\CompoEcommerceBundle(),
            new \Compo\CurrencyBundle\CompoCurrencyBundle(),
            new \Compo\FeaturesBundle\CompoFeaturesBundle(),
            new \Compo\ManufactureBundle\CompoManufactureBundle(),
            new \Compo\CountryBundle\CompoCountryBundle(),
            new \Compo\ProductBundle\CompoProductBundle(),
            new \Compo\CatalogBundle\CompoCatalogBundle(),
            new \Compo\SupplierBundle\CompoSupplierBundle(),
            new \Compo\DeliveryBundle\CompoDeliveryBundle(),
            new \Compo\PaymentBundle\CompoPaymentBundle(),
            new \Compo\OrderBundle\CompoOrderBundle(),
            new \Compo\BasketBundle\CompoBasketBundle(),
            new \Compo\TaggingBundle\CompoTaggingBundle(),
            new \Compo\DiscountBundle\CompoDiscountBundle(),
            new \Compo\UnitBundle\CompoUnitBundle(),
            new \Compo\YandexMarketBundle\CompoYandexMarketBundle(),
            new \Compo\GoogleMerchantBundle\CompoGoogleMerchantBundle(),
            new \Compo\ServiceBundle\CompoServiceBundle(),
            new \Compo\PromoCodeBundle\CompoPromoCodeBundle(),
            new \Compo\PromotionBundle\CompoPromotionBundle(),
            new \Compo\CustomerBundle\CompoCustomerBundle(),
            new \Compo\NotificationBundle\CompoNotificationBundle(),
            new \Compo\FaqBundle\CompoFaqBundle(),
            new \Compo\SocialBundle\CompoSocialBundle(),
            new \Compo\FeedbackBundle\CompoFeedbackBundle(),
            new \Compo\SynchronizationBundle\CompoSynchronizationBundle(),

            new \JMS\DiExtraBundle\JMSDiExtraBundle(),
            new \JMS\AopBundle\JMSAopBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new \JMS\TranslationBundle\JMSTranslationBundle(),
            new \JMS\JobQueueBundle\JMSJobQueueBundle(),
        ];

        // Бандлы для test/dev окружения
        if (\in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new \Symfony\Bundle\WebServerBundle\WebServerBundle();
            $bundles[] = new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();

        }

        return $bundles;
    }

    /**
     * @param LoaderInterface $loader
     *
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // Подгрузка конфига, в зависимости от окружения
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return \dirname($this->getRootDir()) . '/var/cache/' . $this->getEnvironment();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return \dirname($this->getRootDir()) . '/var/logs';
    }

    /**
     * @return string
     */
    public function getProjectName()
    {
        return 'CompoCore';
    }

    /**
     * @return string
     */
    public function getProjectVersionPath()
    {
        return $this->getProjectDir() . '/VERSION';
    }

    /**
     * @return string
     */
    public function getProjectVersion()
    {
        if (file_exists($this->getProjectVersionPath())) {
            return trim(file_get_contents($this->getProjectVersionPath()));
        }

        return '0.0.0';
    }
}
