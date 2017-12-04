<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Tests\Configuration;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\ThemeBundle\Configuration\ThemeConfiguration;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ThemeConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_requires_only_name()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array('name' => 'example/sylius-theme'),
            ),
            array('name' => 'example/sylius-theme'),
            'name'
        );
    }

    /**
     * @test
     */
    public function its_name_is_required_and_cannot_be_empty()
    {
        $this->assertPartialConfigurationIsInvalid(
            array(
                array(/* no name defined */),
            ),
            'name'
        );

        $this->assertPartialConfigurationIsInvalid(
            array(
                array('name' => ''),
            ),
            'name'
        );
    }

    /**
     * @test
     */
    public function its_title_is_optional_but_cannot_be_empty()
    {
        $this->assertPartialConfigurationIsInvalid(
            array(
                array('title' => ''),
            ),
            'title'
        );

        $this->assertConfigurationIsValid(
            array(
                array('title' => 'Lorem ipsum'),
            ),
            'title'
        );
    }

    /**
     * @test
     */
    public function its_description_is_optional_but_cannot_be_empty()
    {
        $this->assertPartialConfigurationIsInvalid(
            array(
                array('description' => ''),
            ),
            'description'
        );

        $this->assertConfigurationIsValid(
            array(
                array('description' => 'Lorem ipsum dolor sit amet'),
            ),
            'description'
        );
    }

    /**
     * @test
     */
    public function its_path_is_optional_but_cannot_be_empty()
    {
        $this->assertPartialConfigurationIsInvalid(
            array(
                array('path' => ''),
            ),
            'path'
        );

        $this->assertConfigurationIsValid(
            array(
                array('path' => '/theme/path'),
            ),
            'path'
        );
    }

    /**
     * @test
     */
    public function its_authors_are_optional()
    {
        $this->assertConfigurationIsValid(
            array(
                array(/* no authors defined */),
            ),
            'authors'
        );
    }

    /**
     * @test
     */
    public function its_author_can_have_only_name_email_homepage_and_role_properties()
    {
        $this->assertConfigurationIsValid(
            array(
                array('authors' => array(array('name' => 'Kamil Kokot'))),
            ),
            'authors'
        );

        $this->assertConfigurationIsValid(
            array(
                array('authors' => array(array('email' => 'kamil@kokot.me'))),
            ),
            'authors'
        );

        $this->assertConfigurationIsValid(
            array(
                array('authors' => array(array('homepage' => 'http://kamil.kokot.me'))),
            ),
            'authors'
        );

        $this->assertConfigurationIsValid(
            array(
                array('authors' => array(array('role' => 'Developer'))),
            ),
            'authors'
        );

        $this->assertPartialConfigurationIsInvalid(
            array(
                array('authors' => array(array('undefined' => '42'))),
            ),
            'authors'
        );
    }

    /**
     * @test
     */
    public function its_author_must_have_at_least_one_property()
    {
        $this->assertPartialConfigurationIsInvalid(
            array(
                array('authors' => array(array(/* empty author */))),
            ),
            'authors',
            'Author cannot be empty'
        );
    }

    /**
     * @test
     */
    public function its_authors_replaces_other_authors_defined_elsewhere()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array('authors' => array(array('name' => 'Kamil Kokot'))),
                array('authors' => array(array('name' => 'Krzysztof Krawczyk'))),
            ),
            array('authors' => array(array('name' => 'Krzysztof Krawczyk'))),
            'authors'
        );
    }

    /**
     * @test
     */
    public function it_ignores_undefined_root_level_fields()
    {
        $this->assertConfigurationIsValid(
            array(
                array('name' => 'example/sylius-theme', 'undefined_variable' => '42'),
            )
        );
    }

    /**
     * @test
     */
    public function its_parents_are_optional_but_has_to_have_at_least_one_element()
    {
        $this->assertConfigurationIsValid(
            array(
                array(),
            ),
            'parents'
        );

        $this->assertPartialConfigurationIsInvalid(
            array(
                array('parents' => array(/* no elements */)),
            ),
            'parents'
        );
    }

    /**
     * @test
     */
    public function its_parent_is_strings()
    {
        $this->assertConfigurationIsValid(
            array(
                array('parents' => array('example/parent-theme', 'example/parent-theme-2')),
            ),
            'parents'
        );
    }

    /**
     * @test
     */
    public function its_parent_cannot_be_empty()
    {
        $this->assertPartialConfigurationIsInvalid(
            array(
                array('parents' => array('')),
            ),
            'parents'
        );
    }

    /**
     * @test
     */
    public function its_parents_replaces_other_parents_defined_elsewhere()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array('parents' => array('example/first-theme')),
                array('parents' => array('example/second-theme')),
            ),
            array('parents' => array('example/second-theme')),
            'parents'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_are_strings()
    {
        $this->assertConfigurationIsValid(
            array(
                array('screenshots' => array('screenshot/krzysztof-krawczyk.jpg', 'screenshot/ryszard-rynkowski.jpg')),
            ),
            'screenshots'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_are_optional()
    {
        $this->assertConfigurationIsValid(
            array(
                array(/* no screenshots defined */),
            ),
            'screenshots'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_must_have_at_least_one_element()
    {
        $this->assertPartialConfigurationIsInvalid(
            array(
                array('screenshots' => array(/* no elements */)),
            ),
            'screenshots'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_cannot_be_empty()
    {
        $this->assertPartialConfigurationIsInvalid(
            array(
                array('screenshots' => array('')),
            ),
            'screenshots'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_replaces_other_screenshots_defined_elsewhere()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array('screenshots' => array('screenshot/zbigniew-holdys.jpg')),
                array('screenshots' => array('screenshot/maryla-rodowicz.jpg')),
            ),
            array('screenshots' => array(array('path' => 'screenshot/maryla-rodowicz.jpg'))),
            'screenshots'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_are_an_array()
    {
        $this->assertConfigurationIsValid(
            array(
                array('screenshots' => array(array('path' => 'screenshot/rick-astley.jpg'))),
            ),
            'screenshots'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_must_have_a_path()
    {
        $this->assertPartialConfigurationIsInvalid(
            array(
                array('screenshots' => array(array('title' => 'Candy shop'))),
            ),
            'screenshots'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_have_optional_title_and_description()
    {
        $this->assertConfigurationIsValid(
            array(
                array('screenshots' => array(array(
                    'path' => 'screenshot/rick-astley.jpg',
                    'title' => 'Rick Astley',
                    'description' => 'He\'ll never gonna give you up or let you down',
                ))),
            ),
            'screenshots'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ThemeConfiguration();
    }
}
