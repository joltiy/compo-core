<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\FeedbackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Feedback.
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)

 * @ORM\Table(name="feedback_messages")
 * @ORM\Entity(repositoryClass="Compo\FeedbackBundle\Repository\FeedbackRepository")
 */
class Feedback
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\TimestampableEntityTrait;

    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * Описание.
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $message;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phone;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(type="json", nullable=true)
     */
    protected $data = [];

    /**
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

    /**
     * Feedback constructor.
     */
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
     * Add tag.
     *
     * @param \Compo\FeedbackBundle\Entity\FeedbackTag $tag
     *
     * @return Feedback
     */
    public function addTag(FeedbackTag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag.
     *
     * @param \Compo\FeedbackBundle\Entity\FeedbackTag $tag
     */
    public function removeTag(FeedbackTag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags.
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

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->getCreatedAt()) {
            return $this->getCreatedAt()->format('Y-m-d H:i:s');
        }

        return 'New';
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
     * @return string
     */
    public function getData()
    {
        if (null === $this->data) {
            $this->data = [];
        }

        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
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

    /**
     * @param array $items
     */
    public function setTags($items = [])
    {
        $remove = [];

        $ids = [];

        foreach ($items as $itemKey => $item) {
            $ids[] = $item->getId();
        }

        foreach ($this->getTags() as $item) {
            if (!\in_array($item->getId(), $ids)) {
                $remove[] = $item;
            }
        }

        foreach ($items as $itemKey => $item) {
            foreach ($this->getTags() as $itemCurrent) {
                if ($itemCurrent->getId() === $item->getId()) {
                    unset($items[$itemKey]);
                    continue;
                }
            }
        }

        foreach ($items as $item) {
            $this->addTag($item);
        }

        foreach ($remove as $item) {
            $this->removeTag($item);
        }
    }
}
