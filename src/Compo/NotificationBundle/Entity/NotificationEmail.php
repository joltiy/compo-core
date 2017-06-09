<?php

namespace Compo\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="notification_email")
 * @ORM\Entity(repositoryClass="Compo\NotificationBundle\Repository\NotificationEmailRepository")
 */
class NotificationEmail
{
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
    protected $note = '';

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
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

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
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }


    public function __toString()
    {
        if ($this->note) {
            return $this->note;
        } elseif($this->id) {
            return (string)$this->id;
        } else {
            return '';
        }
    }
}