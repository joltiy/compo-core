<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\UserBundle\Entity;

use Sonata\UserBundle\Entity\BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * This file has been generated by the Sonata EasyExtends bundle.
 *
 * @link   https://sonata-project.org/bundles/easy-extends
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @ORM\Entity
 * @ORM\Table(name="fos_user_user")
 *
 * @author <yourname> <youremail>
 */
class User extends BaseUser
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var array $settings
     *
     * @ORM\Column(type="json_array")
     */
    public $settings = array();

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return array
     */
    public function getSettings(): array
    {
        $settings = $this->settings;

        if (!is_array($settings)) {
            $this->settings = array();
        }

        return $this->settings;
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }




}
