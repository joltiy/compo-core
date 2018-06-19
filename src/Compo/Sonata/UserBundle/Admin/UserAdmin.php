<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\UserBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Admin\Traits\BaseAdminTrait;
use Compo\Sonata\UserBundle\Form\Type\SecurityRolesType;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\DatePickerType;

/**
 * Class UserAdmin.
 */
class UserAdmin extends \Sonata\UserBundle\Admin\Model\UserAdmin
{
    use BaseAdminTrait;

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        parent::configureFormFields($formMapper);

        $formMapper->removeGroup('Social', 'User');
        $formMapper->removeGroup('Keys', 'Security');

        $formMapper->remove('website');
        $formMapper->remove('biography');
        $formMapper->remove('locale');

        $formMapper->tab('Security');
        $formMapper->with('Roles');

        $formMapper->add('realRoles', SecurityRolesType::class, [
            'expanded' => true,
            'multiple' => true,
            'required' => false,
            'label' => false,
        ]);

        $formMapper->end();
        $formMapper->end();

        $formMapper->tab('User');
        $formMapper->with('Profile');

        $now = new \DateTime();

        $formMapper->add(
            'dateOfBirth',
            DatePickerType::class,
            [
                'format' => 'dd.MM.y',
                'dp_min_date' => '1-1-1900',
                'dp_max_date' => $now->format('c'),
                'required' => false,
            ]
        );

        $formMapper->add('id');

        $keys = $formMapper->keys();

        usort($keys, function ($a, $b) {
            if ('id' === $a) {
                return -1;
            }

            if ('id' === $b) {
                return 1;
            }

            return 0;
        });

        $formMapper->reorder($keys);

        $formMapper->end();
        $formMapper->end();

        $formGroups = $this->getFormGroups();

        $formGroups['Security.Status']['class'] = 'col-md-6';
        $formGroups['Security.Groups']['class'] = 'col-md-6';
        $formGroups['Security.Groups']['label'] = false;

        $this->setFormGroups($formGroups);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        parent::configureListFields($listMapper);

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper->remove('impersonating');

            if (!$listMapper->has('_action')) {
                $listMapper->add('_action');
            }

            $_action = $listMapper->get('_action');
            $options = $_action->getOptions();

            $options['actions']['impersonating'] = [
                'template' => 'SonataAdminBundle:CRUD:list__action_impersonating.html.twig',
            ];

            $_action->setOptions($options);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper): void
    {
        parent::configureDatagridFilters($filterMapper);

        $filterMapper->remove('groups');

        $filterMapper
            ->add(
                'groups',
                'doctrine_orm_model_autocomplete',
                [],
                null,
                [
                    'multiple' => true,
                    'property' => 'name',
                    'minimum_input_length' => 0,
                    'cache' => true,
                    'items_per_page' => 0,
                    'callback' => function ($admin, $property, $value) {
                        /** @var AbstractAdmin $admin */
                        $datagrid = $admin->getDatagrid();

                        /** @var QueryBuilder $queryBuilder */
                        $queryBuilder = $datagrid->getQuery();

                        $queryBuilder->orderBy($queryBuilder->getRootAliases()[0] . '.name', 'ASC');
                        $datagrid->setValue($property, null, $value);
                    },
                ]
            );
    }
}
