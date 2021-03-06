<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Model\Theme;
use Sylius\Bundle\ThemeBundle\Model\ThemeAuthor;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeScreenshot;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ThemeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('theme/name', '/theme/path');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Theme::class);
    }

    public function it_implements_theme_interface()
    {
        $this->shouldImplement(ThemeInterface::class);
    }

    public function its_name_cannot_have_underscores()
    {
        $this->beConstructedWith('first_theme/name', '/theme/path');

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    public function it_has_immutable_name()
    {
        $this->getName()->shouldReturn('theme/name');
    }

    public function its_name_might_contain_numbers()
    {
        $this->beConstructedWith('1e/e7', '/theme/path');

        $this->getName()->shouldReturn('1e/e7');
    }

    public function its_name_might_contain_uppercase_characters()
    {
        $this->beConstructedWith('AbC/DeF', '/theme/path');

        $this->getName()->shouldReturn('AbC/DeF');
    }

    public function it_has_immutable_path()
    {
        $this->getPath()->shouldReturn('/theme/path');
    }

    public function it_has_title()
    {
        $this->getTitle()->shouldReturn(null);

        $this->setTitle('Foo Bar');
        $this->getTitle()->shouldReturn('Foo Bar');
    }

    public function it_has_description()
    {
        $this->getDescription()->shouldReturn(null);

        $this->setDescription('Lorem ipsum.');
        $this->getDescription()->shouldReturn('Lorem ipsum.');
    }

    public function it_has_authors()
    {
        $themeAuthor = new ThemeAuthor();

        $this->getAuthors()->shouldHaveCount(0);

        $this->addAuthor($themeAuthor);
        $this->getAuthors()->shouldHaveCount(1);

        $this->removeAuthor($themeAuthor);
        $this->getAuthors()->shouldHaveCount(0);
    }

    public function it_has_parents(ThemeInterface $theme)
    {
        $this->getParents()->shouldHaveCount(0);

        $this->addParent($theme);
        $this->getParents()->shouldHaveCount(1);

        $this->removeParent($theme);
        $this->getParents()->shouldHaveCount(0);
    }

    public function it_has_screenshots()
    {
        $themeScreenshot = new ThemeScreenshot('some path');

        $this->getScreenshots()->shouldHaveCount(0);

        $this->addScreenshot($themeScreenshot);
        $this->getScreenshots()->shouldHaveCount(1);

        $this->removeScreenshot($themeScreenshot);
        $this->getScreenshots()->shouldHaveCount(0);
    }
}
