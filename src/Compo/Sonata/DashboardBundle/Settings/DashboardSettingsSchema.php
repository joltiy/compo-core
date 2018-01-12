<?php

namespace Compo\Sonata\DashboardBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Compo\Sonata\DashboardBundle\Entity\Dashboard;
use Compo\Sonata\DashboardBundle\Entity\DashboardRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * {@inheritdoc}
 */
class DashboardSettingsSchema extends BaseBundleAdminSettingsSchema
{
    public function getDefaultSettings()
    {
        return [
            'allow_create' => false,
            'default' => null,
        ];
    }

    /**
     * @throws \Exception
     *
     * @return DashboardRepository
     */
    public function getDashboardRepository()
    {
        return $this->getDoctrine()->getRepository(Dashboard::class);
    }

    /**
     * {@inheritdoc}
     */
    public function buildFormSettings()
    {
        $tab = $this->addTab('main');
        $tab->add('allow_create', \Sonata\CoreBundle\Form\Type\BooleanType::class, [
            'translation_domain' => 'SonataDashboardBundle',
        ]);
        $tab->add(
            'default',
            ChoiceType::class,
            [
                'translation_domain' => 'SonataDashboardBundle',
                'choices' => $this->getDashboardRepository()->getChoices(),
            ]
        );
    }
}
