<?php

namespace Compo\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Notification.
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="notification_email_account")
 * @ORM\Entity(repositoryClass="Compo\NotificationBundle\Repository\NotificationEmailAccountRepository")
 */
class NotificationEmailAccount
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $hostname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $transport;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $port;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $encryption;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $authMode;

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param string $hostname
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * @return string
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param string $transport
     */
    public function setTransport($transport)
    {
        $this->transport = $transport;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getEncryption()
    {
        return $this->encryption;
    }

    /**
     * @param string $encryption
     */
    public function setEncryption($encryption)
    {
        $this->encryption = $encryption;
    }

    /**
     * @return string
     */
    public function getAuthMode()
    {
        return $this->authMode;
    }

    /**
     * @param string $auth_mode
     */
    public function setAuthMode($auth_mode)
    {
        $this->authMode = $auth_mode;
    }
}
