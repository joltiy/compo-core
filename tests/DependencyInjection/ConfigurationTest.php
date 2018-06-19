<?php

namespace Compo\Tests;

use PHPUnit\Framework\TestCase;
use Compo\CoreBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testOptions()
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), [[

        ]]);

        $this->assertSame('default', $config['theme']['name']);
    }
}
