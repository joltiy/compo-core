<?php

namespace Compo\Sonata\DashboardBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Compo\CurrencyBundle\Entity\CurrencyRepository;
use Compo\Sonata\DashboardBundle\Entity\Dashboard;
use Compo\Sonata\DashboardBundle\Entity\DashboardRepository;
use Compo\TaggingBundle\Form\TaggingType;
use Compo\UnitBundle\Entity\UnitRepository;
use Doctrine\DBAL\Types\BooleanType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * {@inheritdoc}
 */
class DashboardSettingsSchema extends BaseBundleAdminSettingsSchema
{
    public function getDefaultSettings()
    {
        return array(
            'allow_create' => false,
            'default' => null,
        );
    }

    /**
     * @return DashboardRepository
     *
     * @throws \Exception
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
        $tab->add('allow_create', \Sonata\CoreBundle\Form\Type\BooleanType::class, array(
            'translation_domain' => 'SonataDashboardBundle'
        ));
        $tab->add(
            'default',
            ChoiceType::class,
            array(
                'translation_domain' => 'SonataDashboardBundle',
                'choices' => $this->getDashboardRepository()->getChoices(),
            )
        );
    }


}
