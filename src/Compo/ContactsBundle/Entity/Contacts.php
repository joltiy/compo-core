<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ContactsBundle\Entity;

/**
 * Contacts.
 */
class Contacts
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $worktime;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $bankProps;

    /**
     * @var string
     */
    private $walkInstruction;

    /**
     * @var string
     */
    private $carInstruction;

    /**
     * @var string
     */
    private $mapsCode;

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return Contacts
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get worktime.
     *
     * @return string
     */
    public function getWorktime()
    {
        return $this->worktime;
    }

    /**
     * Set worktime.
     *
     * @param string $worktime
     *
     * @return Contacts
     */
    public function setWorktime($worktime)
    {
        $this->worktime = $worktime;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     *
     * @return Contacts
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Contacts
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get bankProps.
     *
     * @return string
     */
    public function getBankProps()
    {
        return $this->bankProps;
    }

    /**
     * Set bankProps.
     *
     * @param string $bankProps
     *
     * @return Contacts
     */
    public function setBankProps($bankProps)
    {
        $this->bankProps = $bankProps;

        return $this;
    }

    /**
     * Get walkInstruction.
     *
     * @return string
     */
    public function getWalkInstruction()
    {
        return $this->walkInstruction;
    }

    /**
     * Set walkInstruction.
     *
     * @param string $walkInstruction
     *
     * @return Contacts
     */
    public function setWalkInstruction($walkInstruction)
    {
        $this->walkInstruction = $walkInstruction;

        return $this;
    }

    /**
     * Get carInstruction.
     *
     * @return string
     */
    public function getCarInstruction()
    {
        return $this->carInstruction;
    }

    /**
     * Set carInstruction.
     *
     * @param string $carInstruction
     *
     * @return Contacts
     */
    public function setCarInstruction($carInstruction)
    {
        $this->carInstruction = $carInstruction;

        return $this;
    }

    /**
     * Get mapsCode.
     *
     * @return string
     */
    public function getMapsCode()
    {
        return $this->mapsCode;
    }

    /**
     * Set mapsCode.
     *
     * @param string $mapsCode
     *
     * @return Contacts
     */
    public function setMapsCode($mapsCode)
    {
        $this->mapsCode = $mapsCode;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }


}
