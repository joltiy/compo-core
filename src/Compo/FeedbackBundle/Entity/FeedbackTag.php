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
 * Tag.
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Compo\FeedbackBundle\Entity\FeedbackTagRepository")
 */
class FeedbackTag
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ColorEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\TimestampableEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;

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

    /**
     * @var int
     */
    protected $feedbacksCount = 0;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->feedbacks = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add feedbacks.
     *
     * @param \Compo\FeedbackBundle\Entity\Feedback $feedbacks
     *
     * @return FeedbackTag
     */
    public function addFeedback(Feedback $feedbacks)
    {
        $this->feedbacks[] = $feedbacks;

        return $this;
    }

    /**
     * Remove feedbacks.
     *
     * @param \Compo\FeedbackBundle\Entity\Feedback $feedbacks
     */
    public function removeFeedback(Feedback $feedbacks)
    {
        $this->feedbacks->removeElement($feedbacks);
    }

    /**
     * Get feedbacks.
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
