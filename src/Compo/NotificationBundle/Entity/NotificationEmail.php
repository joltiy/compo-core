<?php

namespace Compo\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Notification.
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="notification_email")
 * @ORM\Entity(repositoryClass="Compo\NotificationBundle\Repository\NotificationEmailRepository")
 */
class NotificationEmail
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $event;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, options={"default": ""})
     */
    protected $code = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $recipient;

    /**
     * @ORM\ManyToOne(targetEntity="Compo\NotificationBundle\Entity\NotificationEmailAccount", fetch="EAGER")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $sender;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $subject;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    protected $body;

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param string $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param string $recipient
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * @return NotificationEmailAccount
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param mixed $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->name) {
            return $this->name;
        }

        if ($this->id) {
            return (string) $this->id;
        }

        return '';
    }
}
