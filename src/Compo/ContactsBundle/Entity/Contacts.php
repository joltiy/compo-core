<?php

namespace Compo\ContactsBundle\Entity;

/**
 * Contacts
 */
class Contacts
{
    /**
     * @var integer
     */
    private $id;

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
    private $bankprops;

    /**
     * @var string
     */
    private $walk_instruction;

    /**
     * @var string
     */
    private $car_instruction;

    /**
     * @var string
     */
    private $maps_code;
    /**
     * @var string
     */
    private $cix;
    /**
     * @var string
     */
    private $ciy;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address
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
     * Get worktime
     *
     * @return string
     */
    public function getWorktime()
    {
        return $this->worktime;
    }

    /**
     * Set worktime
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
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set phone
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
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
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
     * Get bankprops
     *
     * @return string
     */
    public function getBankprops()
    {
        return $this->bankprops;
    }

    /**
     * Set bankprops
     *
     * @param string $bankprops
     *
     * @return Contacts
     */
    public function setBankprops($bankprops)
    {
        $this->bankprops = $bankprops;

        return $this;
    }

    /**
     * Get walkInstruction
     *
     * @return string
     */
    public function getWalkInstruction()
    {
        return $this->walk_instruction;
    }

    /**
     * Set walkInstruction
     *
     * @param string $walkInstruction
     *
     * @return Contacts
     */
    public function setWalkInstruction($walkInstruction)
    {
        $this->walk_instruction = $walkInstruction;

        return $this;
    }

    /**
     * Get carInstruction
     *
     * @return string
     */
    public function getCarInstruction()
    {
        return $this->car_instruction;
    }

    /**
     * Set carInstruction
     *
     * @param string $carInstruction
     *
     * @return Contacts
     */
    public function setCarInstruction($carInstruction)
    {
        $this->car_instruction = $carInstruction;

        return $this;
    }

    /**
     * Get mapsCode
     *
     * @return string
     */
    public function getMapsCode()
    {
        return $this->maps_code;
    }

    /**
     * Set mapsCode
     *
     * @param string $mapsCode
     *
     * @return Contacts
     */
    public function setMapsCode($mapsCode)
    {
        $this->maps_code = $mapsCode;

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
     * Get cix
     *
     * @return string
     */
    public function getCix()
    {
        return $this->cix;
    }

    /**
     * Set cix
     *
     * @param string $cix
     *
     * @return Contacts
     */
    public function setCix($cix)
    {
        $this->cix = $cix;

        return $this;
    }

    /**
     * Get ciy
     *
     * @return string
     */
    public function getCiy()
    {
        return $this->ciy;
    }

    /**
     * Set ciy
     *
     * @param string $ciy
     *
     * @return Contacts
     */
    public function setCiy($ciy)
    {
        $this->ciy = $ciy;

        return $this;
    }
}
