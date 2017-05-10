<?php

namespace Compo\ContactsBundle\Entity;


use \Compo\SeoBundle\Entity\Traits\SeoEntity;
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
    private $social_vk;

    /**
     * @var string
     */
    private $social_fb;

    /**
     * @var string
     */
    private $social_yt;

    /**
     * @var string
     */
    private $social_tw;

    /**
     * @var string
     */
    private $social_ig;

    /**
     * @var string
     */
    private $social_gg;

    /**
     * @var string
     */
    private $maps_code;


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
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
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
     * Get worktime
     *
     * @return string
     */
    public function getWorktime()
    {
        return $this->worktime;
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
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
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
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
     * Get bankprops
     *
     * @return string
     */
    public function getBankprops()
    {
        return $this->bankprops;
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
     * Get walkInstruction
     *
     * @return string
     */
    public function getWalkInstruction()
    {
        return $this->walk_instruction;
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
     * Get carInstruction
     *
     * @return string
     */
    public function getCarInstruction()
    {
        return $this->car_instruction;
    }

    /**
     * Set socialVk
     *
     * @param string $socialVk
     *
     * @return Contacts
     */
    public function setSocialVk($socialVk)
    {
        $this->social_vk = $socialVk;

        return $this;
    }

    /**
     * Get socialVk
     *
     * @return string
     */
    public function getSocialVk()
    {
        return $this->social_vk;
    }

    /**
     * Set socialFb
     *
     * @param string $socialFb
     *
     * @return Contacts
     */
    public function setSocialFb($socialFb)
    {
        $this->social_fb = $socialFb;

        return $this;
    }

    /**
     * Get socialFb
     *
     * @return string
     */
    public function getSocialFb()
    {
        return $this->social_fb;
    }

    /**
     * Set socialYt
     *
     * @param string $socialYt
     *
     * @return Contacts
     */
    public function setSocialYt($socialYt)
    {
        $this->social_yt = $socialYt;

        return $this;
    }

    /**
     * Get socialYt
     *
     * @return string
     */
    public function getSocialYt()
    {
        return $this->social_yt;
    }

    /**
     * Set socialTw
     *
     * @param string $socialTw
     *
     * @return Contacts
     */
    public function setSocialTw($socialTw)
    {
        $this->social_tw = $socialTw;

        return $this;
    }

    /**
     * Get socialTw
     *
     * @return string
     */
    public function getSocialTw()
    {
        return $this->social_tw;
    }

    /**
     * Set socialIg
     *
     * @param string $socialIg
     *
     * @return Contacts
     */
    public function setSocialIg($socialIg)
    {
        $this->social_ig = $socialIg;

        return $this;
    }

    /**
     * Get socialIg
     *
     * @return string
     */
    public function getSocialIg()
    {
        return $this->social_ig;
    }

    /**
     * Set socialGg
     *
     * @param string $socialGg
     *
     * @return Contacts
     */
    public function setSocialGg($socialGg)
    {
        $this->social_gg = $socialGg;

        return $this;
    }

    /**
     * Get socialGg
     *
     * @return string
     */
    public function getSocialGg()
    {
        return $this->social_gg;
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
     * Get mapsCode
     *
     * @return string
     */
    public function getMapsCode()
    {
        return $this->maps_code;
    }

    public function __toString() {
        return '';
    }
}

