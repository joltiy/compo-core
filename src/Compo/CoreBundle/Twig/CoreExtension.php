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
            new \Twig_SimpleFunction('project_version', [$this, 'getProjectVersion'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'compo_core_extension';
    }
}
