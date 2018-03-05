<?php

namespace Compo\CoreBundle\Twig;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

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

    public function getAdminObjectSourceName($item)
    {
        $admin = $this->getContainer()->get('sonata.admin.pool')->getAdminByClass($item->getSourceClass());

        if ($admin->getObject($item->getSourceItemId())) {
            return $admin->getObject($item->getSourceItemId())->getValue();
        }
    }

    public function getAdminObjectUrl($item)
    {
        $admin = $this->getContainer()->get('sonata.admin.pool')->getAdminByClass($item->getSourceClass());

        if ($admin->getObject($item->getSourceItemId())) {
            return $admin->generateObjectUrl('edit', $admin->getObject($item->getSourceItemId()));
        }
    }

    public function getAdminObjectTargetName($item)
    {
        $admin = $this->getContainer()->get('sonata.admin.pool')->getAdminByClass($item->getClass());

        return $admin->getObject($item->getTargetId())->getName();
    }

    public function getAdminObjectTargetUrl($item)
    {
        $admin = $this->getContainer()->get('sonata.admin.pool')->getAdminByClass($item->getClass());

        return $admin->generateObjectUrl('edit', $admin->getObject($item->getTargetId()));
    }

    public function getJsonPrettyPrintUnicode($data) {
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
