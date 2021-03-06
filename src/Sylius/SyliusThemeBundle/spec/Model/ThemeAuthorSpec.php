<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Model\ThemeAuthor;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ThemeAuthorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ThemeAuthor::class);
    }

    public function it_implements_theme_author_interface()
    {
        $this->shouldImplement(ThemeAuthor::class);
    }

    public function it_has_name()
    {
        $this->getName()->shouldReturn(null);

        $this->setName('Krzysztof Krawczyk');
        $this->getName()->shouldReturn('Krzysztof Krawczyk');
    }

    public function it_has_email()
    {
        $this->getEmail()->shouldReturn(null);

        $this->setEmail('cristopher@example.com');
        $this->getEmail()->shouldReturn('cristopher@example.com');
    }

    public function it_has_homepage()
    {
        $this->getHomepage()->shouldReturn(null);

        $this->setHomepage('http://example.com');
        $this->getHomepage()->shouldReturn('http://example.com');
    }

    public function it_has_role()
    {
        $this->getRole()->shouldReturn(null);

        $this->setRole('Developer');
        $this->getRole()->shouldReturn('Developer');
    }
}
