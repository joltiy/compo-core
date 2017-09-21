<?php

namespace Compo\ArticlesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Articles
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @ORM\Table(name="articles")
 * @ORM\Entity(repositoryClass="Compo\ArticlesBundle\Repository\ArticlesRepository")
 */
class Articles
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BodyEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ImageEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ViewsEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\PublicationAtEntityTrait;
    use \Compo\SeoBundle\Entity\Traits\SeoEntity;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
}

