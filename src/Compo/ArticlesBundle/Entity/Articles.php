<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ArticlesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Articles.
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
    use \Compo\Sonata\AdminBundle\Entity\ImageEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ViewsEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\PublicationAtEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\TimestampableEntityTrait;
    use \Compo\SeoBundle\Entity\Traits\SeoEntity;

    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * Текст
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body;

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
}
