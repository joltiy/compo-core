<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Asset;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Asset\PathResolver;
use Sylius\Bundle\ThemeBundle\Asset\PathResolverInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class PathResolverSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(PathResolver::class);
    }

    public function it_implements_path_resolver_interface()
    {
        $this->shouldImplement(PathResolverInterface::class);
    }

    public function it_returns_modified_path_if_its_referencing_bundle_asset(ThemeInterface $theme)
    {
        $theme->getName()->willReturn('theme/name');

        $this->resolve('bundles/asset.min.js', $theme)->shouldReturn('bundles/_themes/theme/name/asset.min.js');
    }

    public function it_does_not_change_path_if_its_not_referencing_bundle_asset(ThemeInterface $theme)
    {
        $this->resolve('/long.path/asset.min.js', $theme)->shouldReturn('/long.path/asset.min.js');
    }
}
