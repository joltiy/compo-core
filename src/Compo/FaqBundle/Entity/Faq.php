<?php

namespace Compo\FaqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Faq
 *
 * @ORM\Table(name="faq")
 * @ORM\Entity(repositoryClass="Compo\FaqBundle\Repository\FaqRepository")
 */
class Faq
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ImageEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ViewsEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\PublicationAtEntityTrait;

    use \Compo\SeoBundle\Entity\Traits\SeoEntity;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * Описание
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $answer;

    /**
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param string $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }
}

