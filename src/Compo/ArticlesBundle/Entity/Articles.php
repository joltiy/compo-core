<?php

namespace Compo\ArticlesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Articles
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @ORM\Table(name="articles",
indexes={
 *
 *          @ORM\Index(name="publication_at_enabled_deleted_at", columns={"publication_at","enabled","deleted_at" }),

 *          @ORM\Index(name="publication_at", columns={"publication_at" }),
 *          @ORM\Index(name="enabled", columns={"enabled" }),
 *          @ORM\Index(name="deleted_at", columns={"deleted_at" }),
 *
 *          @ORM\Index(name="enabled_deleted_at", columns={"enabled", "deleted_at" })
 *     }
 * )
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

