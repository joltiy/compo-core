<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\DashboardBundle\Entity;

use Compo\Sonata\UserBundle\Entity\Group;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\DashboardBundle\Entity\BaseDashboard;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @ORM\Entity(repositoryClass="Compo\Sonata\DashboardBundle\Entity\DashboardRepository")
 * @ORM\Table(name="dashboard__dashboard")
 */
class Dashboard extends BaseDashboard
{
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * @ORM\ManyToMany(targetEntity="Compo\Sonata\UserBundle\Entity\Group", indexBy="id")
     * @ORM\JoinTable(name="dashboard__dashboard_groups",
     *      joinColumns={@ORM\JoinColumn(name="dashboard_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     *      )
     */
    protected $userGroups;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Включено.
     *
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    protected $allowEdit = false;

    /**
     * @return bool
     */
    public function isAllowEdit()
    {
        return $this->allowEdit;
    }

    /**
     * Get allowEdit.
     *
     * @return bool
     */
    public function getAllowEdit()
    {
        return $this->allowEdit;
    }

    /**
     * @param bool $allowEdit
     */
    public function setAllowEdit($allowEdit)
    {
        $this->allowEdit = $allowEdit;
    }

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->blocks = [];

        $this->userGroups = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUserGroups()
    {
        return $this->userGroups;
    }

    /**
     * @param mixed $userGroups
     */
    public function setUserGroups($userGroups)
    {
        $this->userGroups = $userGroups;
    }

    /**
     * Add UserGroups.
     *
     * @param Group $group
     *
     * @return self
     */
    public function addUserGroup(Group $group)
    {
        $this->userGroups[] = $group;

        return $this;
    }

    /**
     * Remove UserGroups.
     *
     * @param Group $group
     */
    public function removeUserGroup(Group $group)
    {
        $this->userGroups->removeElement($group);
    }
}
