<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Loader;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoadingFailedException;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ThemeLoadingFailedExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ThemeLoadingFailedException::class);
    }

    public function it_is_a_domain_exception()
    {
        $this->shouldHaveType(\DomainException::class);
    }

    public function it_is_a_logic_exception()
    {
        $this->shouldHaveType(\LogicException::class);
    }
}
