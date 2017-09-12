<?php

namespace Compo\CoreBundle\Kernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class AppKernel
 *
 * @package Compo\CoreBundle\Kernel
 */
class AppKernel extends Kernel
{
    /**
     * @return array
     */
    public function registerBundles()
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $bundles = array(
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
            new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),

            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new \Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new \Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),

            new \FOS\UserBundle\FOSUserBundle(),
            new \FOS\RestBundle\FOSRestBundle(),
            new \FOS\ElasticaBundle\FOSElasticaBundle(),
            new \FOS\JsRoutingBundle\FOSJsRoutingBundle(),


            new \Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new \SimpleThings\EntityAudit\SimpleThingsEntityAuditBundle(),
            new \Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new \Spy\TimelineBundle\SpyTimelineBundle(),
            new \Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            new \Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new \Liip\ImagineBundle\LiipImagineBundle(),
            new \WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new \winzou\Bundle\StateMachineBundle\winzouStateMachineBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new \Pix\SortableBehaviorBundle\PixSortableBehaviorBundle(),
            new \Presta\SitemapBundle\PrestaSitemapBundle(),
            new \KonstantinKuklin\AsseticStaticGzipBundle\AsseticStaticGzipBundle(),

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
            new \Compo\Sonata\NotificationBundle\CompoSonataNotificationBundle(),
            new \Compo\Sonata\PageBundle\CompoSonataPageBundle(),
            new \Compo\Sonata\TimelineBundle\CompoSonataTimelineBundle(),
            new \Compo\Sonata\UserBundle\CompoSonataUserBundle(),
            new \Compo\Sonata\BlockBundle\CompoSonataBlockBundle(),
            new \Compo\Sonata\FormatterBundle\CompoSonataFormatterBundle(),
            new \Compo\Sonata\SeoBundle\CompoSonataSeoBundle(),
            new \Compo\Sonata\DashboardBundle\CompoSonataDashboardBundle(),

            new \Compo\CoreBundle\CompoCoreBundle(),
            new \Compo\SeoBundle\CompoSeoBundle(),
            new \Compo\MenuBundle\CompoMenuBundle(),
            new \Compo\NewsBundle\CompoNewsBundle(),
            new \Compo\ArticlesBundle\CompoArticlesBundle(),
            new \Compo\SmsProviderBundle\CompoSmsProviderBundle(),
            new \Compo\RedirectBundle\CompoRedirectBundle(),
            new \Compo\BannerBundle\CompoBannerBundle(),
            new \Compo\AdvantagesBundle\CompoAdvantagesBundle(),
            new \Compo\ContactsBundle\CompoContactsBundle(),

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
            new \Compo\PromotionBundle\CompoPromotionBundle(),
            new \Compo\CustomerBundle\CompoCustomerBundle(),
            new \Compo\NotificationBundle\CompoNotificationBundle(),
            new \Compo\FaqBundle\CompoFaqBundle(),
            new \Compo\SocialBundle\CompoSocialBundle(),
            new \Compo\FeedbackBundle\CompoFeedbackBundle(),

            new \JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new \JMS\AopBundle\JMSAopBundle(),
            new \JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle($this),
            new \JMS\TranslationBundle\JMSTranslationBundle(),
        );

        // Бандлы для test/dev окружения
        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    /**
     * @param LoaderInterface $loader
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // Подгрузка конфига, в зависимости от окружения
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }

    /**
     * {@inheritDoc}
     */
    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * {@inheritDoc}
     */
    public function getCacheDir()
    {
        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment();
    }

    /**
     * {@inheritDoc}
     */
    public function getLogDir()
    {
        return dirname(__DIR__) . '/var/logs';
    }

    /**
     * {@inheritDoc}
     */
    protected function initializeContainer()
    {
        parent::initializeContainer();
        /*
        if (PHP_SAPI == 'cli') {
            $this->getContainer()->enterScope('request');
            $this->getContainer()->set('request', new \Symfony\Component\HttpFoundation\Request(), 'request');
        }
        */
    }
}
