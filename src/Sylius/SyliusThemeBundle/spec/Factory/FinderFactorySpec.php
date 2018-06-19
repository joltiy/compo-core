<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Factory\FinderFactory;
use Sylius\Bundle\ThemeBundle\Factory\FinderFactoryInterface;
use Symfony\Component\Finder\Finder;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class FinderFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(FinderFactory::class);
    }

    public function it_implements_finder_factory_interface()
    {
        $this->shouldImplement(FinderFactoryInterface::class);
    }

    public function it_creates_a_brand_new_finder()
    {
        $this->create()->shouldHaveType(Finder::class);
    }
}
