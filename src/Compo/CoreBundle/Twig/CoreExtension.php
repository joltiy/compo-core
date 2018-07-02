<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Twig;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\SynchronizationBundle\Entity\SynchronizationImportLog;

/**
 * Class CoreExtension.
 */
class CoreExtension extends \Twig_Extension
{
    use ContainerAwareTrait;

    /**
     * @return string
     */
    public function getProjectVersion()
    {
        return $this->getContainer()->get('kernel')->getProjectVersion();
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('json_pretty_print_unicode', [$this, 'getJsonPrettyPrintUnicode'], ['is_safe' => ['html']]),

            new \Twig_SimpleFunction('admin_object_target_url', [$this, 'getAdminObjectTargetUrl'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('admin_object_target_name', [$this, 'getAdminObjectTargetName'], ['is_safe' => ['html']]),

            new \Twig_SimpleFunction('admin_object_url', [$this, 'getAdminObjectUrl'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('admin_object_source_name', [$this, 'getAdminObjectSourceName'], ['is_safe' => ['html']]),

            new \Twig_SimpleFunction('project_version', [$this, 'getProjectVersion'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param SynchronizationImportLog $item
     * @return null
     */
    public function getAdminObjectSourceName($item)
    {
        /** @var AbstractAdmin $admin */
        $admin = $this->getContainer()->get('sonata.admin.pool')->getAdminByClass($item->getSourceClass());

        if ($admin->getObject($item->getSourceItemId())) {
            return $admin->getObject($item->getSourceItemId())->getValue();
        }

        return null;
    }

    /**
     * @param SynchronizationImportLog $item
     *
     * @return null|string
     */
    public function getAdminObjectUrl($item)
    {
        /** @var AbstractAdmin $admin */
        $admin = $this->getContainer()->get('sonata.admin.pool')->getAdminByClass($item->getSourceClass());

        if ($admin->getObject($item->getSourceItemId())) {
            return $admin->generateObjectUrl('edit', $admin->getObject($item->getSourceItemId()));
        }

        return null;
    }

    /**
     * @param SynchronizationImportLog $item
     *
     * @return mixed
     */
    public function getAdminObjectTargetName($item)
    {
        /** @var AbstractAdmin $admin */
        $admin = $this->getContainer()->get('sonata.admin.pool')->getAdminByClass($item->getClass());

        return $admin->getObject($item->getTargetId())->getName();
    }

    /**
     * @param SynchronizationImportLog $item
     *
     * @return string
     */
    public function getAdminObjectTargetUrl($item)
    {
        /** @var AbstractAdmin $admin */
        $admin = $this->getContainer()->get('sonata.admin.pool')->getAdminByClass($item->getClass());

        return $admin->generateObjectUrl('edit', $admin->getObject($item->getTargetId()));
    }

    /**
     * @param $data
     *
     * @return string
     */
    public function getJsonPrettyPrintUnicode($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'compo_core_extension';
    }
}
