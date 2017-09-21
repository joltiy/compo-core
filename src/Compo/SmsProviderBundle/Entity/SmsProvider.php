<?php

namespace Compo\SmsProviderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SmsProvider
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="sms_provider")
 * @ORM\Entity(repositoryClass="Compo\SmsProviderBundle\Repository\SmsProviderRepository")
 */
class SmsProvider
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $login;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $type;

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
