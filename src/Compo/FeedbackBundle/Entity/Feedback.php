<?php

namespace Compo\FeedbackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Feedback
 *
 * @ORM\Table(name="feedback_messages")
 * @ORM\Entity(repositoryClass="Compo\FeedbackBundle\Repository\FeedbackRepository")
 */
class Feedback
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;

    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;


    /**
     * Описание
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $message;

    /**
     *
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     *
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phone;

    /**
     *
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $email;

    /**
     *
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $page;

    /**
     * @ORM\ManyToMany(targetEntity="Compo\FeedbackBundle\Entity\FeedbackTag", inversedBy="feedbacks")
     * @ORM\JoinTable(name="feedback_tags",
     *      joinColumns={@ORM\JoinColumn(name="feedback_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     */
    protected $tags;

    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Add tag
     *
     * @param \Compo\FeedbackBundle\Entity\FeedbackTag $tag
     *
     * @return Feedback
     */
    public function addTag(\Compo\FeedbackBundle\Entity\FeedbackTag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param \Compo\FeedbackBundle\Entity\FeedbackTag $tag
     */
    public function removeTag(\Compo\FeedbackBundle\Entity\FeedbackTag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }


    public function __toString()
    {
        return $this->getCreatedAt()->format('Y-m-d H:i:s');
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}