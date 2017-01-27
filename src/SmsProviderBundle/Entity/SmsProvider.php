<?php

namespace Compo\SmsProviderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SmsProvider
 *
 * @ORM\Table(name="sms_provider")
 * @ORM\Entity(repositoryClass="Compo\SmsProviderBundle\Repository\SmsProviderRepository")
 */
class SmsProvider
{
    use \Compo\Sonata\AdminBundle\Entity\PositionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\AliasEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;

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
     * Constructor
     */
    public function __construct()
    {
    }

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
