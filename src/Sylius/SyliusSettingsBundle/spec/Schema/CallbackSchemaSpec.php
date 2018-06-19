<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SettingsBundle\Schema;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SettingsBundle\Schema\CallbackSchema;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CallbackSchemaSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(function () {}, function () {});
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CallbackSchema::class);
    }

    public function it_implements_schema_interface()
    {
        $this->shouldImplement(SchemaInterface::class);
    }

    public function it_uses_callback_to_build_settings(SettingsBuilderInterface $settingsBuilder)
    {
        $this->beConstructedWith(function (SettingsBuilderInterface $settingsBuilder) {
            $settingsBuilder->setDefaults(['foo' => 'bar']);
        }, function () {});

        $settingsBuilder->setDefaults(['foo' => 'bar'])->shouldBeCalled();

        $this->buildSettings($settingsBuilder);
    }

    public function it_uses_callback_to_build_form(FormBuilderInterface $formBuilder)
    {
        $this->beConstructedWith(function () {}, function (FormBuilderInterface $formBuilder) {
            $formBuilder->add('bono', 'u2');
        });

        $formBuilder->add('bono', 'u2')->shouldBeCalled();

        $this->buildForm($formBuilder);
    }
}
