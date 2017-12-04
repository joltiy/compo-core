<?php

namespace Compo\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Notification.
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Entity(repositoryClass="Compo\NotificationBundle\Repository\NotificationSmsAccountRepository")
 */
class NotificationSmsAccount
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
    protected $sender;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $transport;

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
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
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
}
