<?php

namespace Compo\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NewsTag
 *
 * @ORM\Table(name="news_tag")
 * @ORM\Entity(repositoryClass="Compo\NewsBundle\Repository\NewsTagRepository")
 */
class NewsTag
{
    use \Compo\Sonata\AdminBundle\Entity\PositionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ColorEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
