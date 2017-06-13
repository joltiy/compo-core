<?php

namespace Compo\AdvantagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Advantages
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @ORM\Table(
 *     indexes={
 *          @ORM\Index(name="deleted_at", columns={"deleted_at" }),
 *          @ORM\Index(name="id_deleted_at", columns={"id", "deleted_at" })
 *     }
 * )
 * @ORM\Entity(repositoryClass="Compo\AdvantagesBundle\Entity\AdvantagesRepository")
 */
class Advantages
{
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;



}
