<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Tests\Functional;

use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@gmail.com>
 */
final class SyliusThemeBundleTest extends KernelTestCase
{
    /**
     * @test
     */
    public function its_services_are_initializable()
    {
        static::bootKernel();

        /** @var Container $container */
        $container = self::$kernel->getContainer();

        $serviceIds = array_filter($container->getServiceIds(), function ($serviceId) {
            return 0 === mb_strpos($serviceId, 'sylius.');
        });

        foreach ($serviceIds as $serviceId) {
            Assert::assertNotNull($container->get($serviceId));
        }
    }
}
