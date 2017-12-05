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
     *
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
        return array(
            new \Twig_SimpleFunction('project_version', array($this, 'getProjectVersion'), array('is_safe' => array('html'))),
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'compo_core_extension';
    }
}
