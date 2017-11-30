<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class TemplatingTest extends WebTestCase
{
    /**
     * @test
     * @dataProvider getBundleTemplates
     *
     * @param string $templateName
     * @param string $contents
     */
    public function it_renders_bundle_templates($templateName, $contents)
    {
        $client = self::createClient();

        $crawler = $client->request('GET', '/template/' . $templateName);
        $this->assertEquals($contents, trim($crawler->text()));
    }

    /**
     * @return array
     */
    public function getBundleTemplates()
    {
        return array(
            array('TestBundle:Templating:vanillaTemplate.txt.twig', 'TestBundle:Templating:vanillaTemplate.txt.twig'),
            array('TestBundle:Templating:vanillaOverriddenTemplate.txt.twig', 'TestBundle:Templating:vanillaOverriddenTemplate.txt.twig (app overridden)'),
            array('TestBundle:Templating:vanillaOverriddenThemeTemplate.txt.twig', 'TestBundle:Templating:vanillaOverriddenThemeTemplate.txt.twig|sylius/first-test-theme'),
            array('TestBundle:Templating:bothThemesTemplate.txt.twig', 'TestBundle:Templating:bothThemesTemplate.txt.twig|sylius/first-test-theme'),
            array('TestBundle:Templating:lastThemeTemplate.txt.twig', 'TestBundle:Templating:lastThemeTemplate.txt.twig|sylius/second-test-theme'),
        );
    }

    /**
     * @test
     * @dataProvider getBundleTemplatesUsingNamespacedPaths
     *
     * @param string $templateName
     * @param string $contents
     */
    public function it_renders_bundle_templates_using_namespaced_paths($templateName, $contents)
    {
        $client = self::createClient();

        $crawler = $client->request('GET', '/template/' . $templateName);
        $this->assertEquals($contents, trim($crawler->text()));
    }

    /**
     * @return array
     */
    public function getBundleTemplatesUsingNamespacedPaths()
    {
        return array(
            array('@Test/Templating/vanillaTemplate.txt.twig', 'TestBundle:Templating:vanillaTemplate.txt.twig'),
            array('@Test/Templating/vanillaOverriddenTemplate.txt.twig', 'TestBundle:Templating:vanillaOverriddenTemplate.txt.twig (app overridden)'),
            array('@Test/Templating/vanillaOverriddenThemeTemplate.txt.twig', 'TestBundle:Templating:vanillaOverriddenThemeTemplate.txt.twig|sylius/first-test-theme'),
            array('@Test/Templating/bothThemesTemplate.txt.twig', 'TestBundle:Templating:bothThemesTemplate.txt.twig|sylius/first-test-theme'),
            array('@Test/Templating/lastThemeTemplate.txt.twig', 'TestBundle:Templating:lastThemeTemplate.txt.twig|sylius/second-test-theme'),
        );
    }

    /**
     * @test
     * @dataProvider getAppTemplates
     *
     * @param string $templateName
     * @param string $contents
     */
    public function it_renders_application_templates($templateName, $contents)
    {
        $client = self::createClient();

        $crawler = $client->request('GET', '/template/' . $templateName);
        $this->assertEquals($contents, trim($crawler->text()));
    }

    /**
     * @return array
     */
    public function getAppTemplates()
    {
        return array(
            array(':Templating:vanillaTemplate.txt.twig', ':Templating:vanillaTemplate.txt.twig'),
            array(':Templating:bothThemesTemplate.txt.twig', ':Templating:bothThemesTemplate.txt.twig|sylius/first-test-theme'),
            array(':Templating:lastThemeTemplate.txt.twig', ':Templating:lastThemeTemplate.txt.twig|sylius/second-test-theme'),
        );
    }

    /**
     * @test
     * @dataProvider getAppTemplatesUsingNamespacedPaths
     *
     * @param string $templateName
     * @param string $contents
     */
    public function it_renders_application_templates_using_namespaced_paths($templateName, $contents)
    {
        $client = self::createClient();

        $crawler = $client->request('GET', '/template/' . $templateName);
        $this->assertEquals($contents, trim($crawler->text()));
    }

    /**
     * @return array
     */
    public function getAppTemplatesUsingNamespacedPaths()
    {
        return array(
            array('/Templating/vanillaTemplate.txt.twig', ':Templating:vanillaTemplate.txt.twig'),
            array('/Templating/bothThemesTemplate.txt.twig', ':Templating:bothThemesTemplate.txt.twig|sylius/first-test-theme'),
            array('/Templating/lastThemeTemplate.txt.twig', ':Templating:lastThemeTemplate.txt.twig|sylius/second-test-theme'),
        );
    }
}
