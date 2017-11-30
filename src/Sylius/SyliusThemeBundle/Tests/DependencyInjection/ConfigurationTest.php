<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\ThemeBundle\DependencyInjection\Configuration;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_has_default_context_service_set()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array(),
            ),
            array('context' => 'sylius.theme.context.settable'),
            'context'
        );
    }

    /**
     * @test
     */
    public function its_context_cannot_be_empty()
    {
        $this->assertPartialConfigurationIsInvalid(
            array(
                array(''),
            ),
            'context'
        );
    }

    /**
     * @test
     */
    public function its_context_can_be_overridden()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array('context' => 'sylius.theme.context.custom'),
            ),
            array('context' => 'sylius.theme.context.custom'),
            'context'
        );
    }

    /**
     * @test
     */
    public function assets_support_is_enabled_by_default()
    {
        $this->assertProcessedConfigurationEquals(array(array()), array('assets' => array('enabled' => true)), 'assets');
    }

    /**
     * @test
     */
    public function assets_support_may_be_toggled()
    {
        $this->assertProcessedConfigurationEquals(array(array('assets' => array('enabled' => true))), array('assets' => array('enabled' => true)), 'assets');
        $this->assertProcessedConfigurationEquals(array(array('assets' => array())), array('assets' => array('enabled' => true)), 'assets');
        $this->assertProcessedConfigurationEquals(array(array('assets' => null)), array('assets' => array('enabled' => true)), 'assets');

        $this->assertProcessedConfigurationEquals(array(array('assets' => array('enabled' => false))), array('assets' => array('enabled' => false)), 'assets');
        $this->assertProcessedConfigurationEquals(array(array('assets' => false)), array('assets' => array('enabled' => false)), 'assets');
    }

    /**
     * @test
     */
    public function templating_support_is_enabled_by_default()
    {
        $this->assertProcessedConfigurationEquals(array(array()), array('templating' => array('enabled' => true)), 'templating');
    }

    /**
     * @test
     */
    public function templating_support_may_be_toggled()
    {
        $this->assertProcessedConfigurationEquals(array(array('templating' => array('enabled' => true))), array('templating' => array('enabled' => true)), 'templating');
        $this->assertProcessedConfigurationEquals(array(array('templating' => array())), array('templating' => array('enabled' => true)), 'templating');
        $this->assertProcessedConfigurationEquals(array(array('templating' => null)), array('templating' => array('enabled' => true)), 'templating');

        $this->assertProcessedConfigurationEquals(array(array('templating' => array('enabled' => false))), array('templating' => array('enabled' => false)), 'templating');
        $this->assertProcessedConfigurationEquals(array(array('templating' => false)), array('templating' => array('enabled' => false)), 'templating');
    }

    /**
     * @test
     */
    public function translations_support_is_enabled_by_default()
    {
        $this->assertProcessedConfigurationEquals(array(array()), array('translations' => array('enabled' => true)), 'translations');
    }

    /**
     * @test
     */
    public function translations_support_may_be_toggled()
    {
        $this->assertProcessedConfigurationEquals(array(array('translations' => array('enabled' => true))), array('translations' => array('enabled' => true)), 'translations');
        $this->assertProcessedConfigurationEquals(array(array('translations' => array())), array('translations' => array('enabled' => true)), 'translations');
        $this->assertProcessedConfigurationEquals(array(array('translations' => null)), array('translations' => array('enabled' => true)), 'translations');

        $this->assertProcessedConfigurationEquals(array(array('translations' => array('enabled' => false))), array('translations' => array('enabled' => false)), 'translations');
        $this->assertProcessedConfigurationEquals(array(array('translations' => false)), array('translations' => array('enabled' => false)), 'translations');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }
}
