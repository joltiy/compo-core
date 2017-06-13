<?php

namespace Compo\FeedbackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Tag
 *
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Compo\FeedbackBundle\Entity\FeedbackTagRepository")
 */
class FeedbackTag
{
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ColorEntityTrait;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;


    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $cssClass;

    /**
     * @ORM\ManyToMany(targetEntity="Compo\FeedbackBundle\Entity\Feedback", mappedBy="tags", cascade={"all"})
     */
    protected $feedbacks;

    protected $feedbacksCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->feedbacks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return int
     */
    public function getFeedbacksCount()
    {
        return $this->feedbacksCount;
    }

    /**
     * @param int $feedbacksCount
     */
    public function setFeedbacksCount($feedbacksCount)
    {
        $this->feedbacksCount = $feedbacksCount;
    }

    /**
     * Add feedbacks
     *
     * @param \Compo\FeedbackBundle\Entity\Feedback $feedbacks
     *
     * @return FeedbackTag
     */
    public function addFeedback(\Compo\FeedbackBundle\Entity\Feedback $feedbacks)
    {
        $this->feedbacks[] = $feedbacks;

        return $this;
    }

    /**
     * Remove feedbacks
     *
     * @param \Compo\FeedbackBundle\Entity\Feedback $feedbacks
     */
    public function removeFeedback(\Compo\FeedbackBundle\Entity\Feedback $feedbacks)
    {
        $this->feedbacks->removeElement($feedbacks);
    }

    /**
     * Get feedbacks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeedbacks()
    {
        return $this->feedbacks;
    }

    /**
     * @return mixed
     */
    public function getCssClass()
    {
        return $this->cssClass;
    }

    /**
     * @param mixed $cssClass
     */
    public function setCssClass($cssClass)
    {
        $this->cssClass = $cssClass;
    }
}
