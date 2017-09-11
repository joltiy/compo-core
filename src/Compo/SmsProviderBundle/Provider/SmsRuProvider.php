<?php

namespace Compo\SmsProviderBundle\Provider;

/**
 * Class SmsRuProvider
 * @package Compo\SmsProviderBundle\Provider
 */
class SmsRuProvider
{
    public $account;

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param mixed $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }
    
    /**
     * @param $phone
     * @param $text
     */
    public function send($phone, $text) {

    }
}