<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SettingsBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManager;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SettingsManagerSpec extends ObjectBehavior
{
    public function let(
        ServiceRegistryInterface $schemaRegistry,
        ServiceRegistryInterface $resolverRegistry,
        ObjectManager $manager,
        FactoryInterface $settingsFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith(
            $schemaRegistry,
            $resolverRegistry,
            $manager,
            $settingsFactory,
            $eventDispatcher
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(SettingsManager::class);
    }

    public function it_should_be_a_settings_manager()
    {
        $this->shouldImplement(SettingsManagerInterface::class);
    }
}
