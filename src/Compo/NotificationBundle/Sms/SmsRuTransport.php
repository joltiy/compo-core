<?php

namespace Compo\NotificationBundle\Sms;

/**
 * Class SmsRuTransport
 * @package Compo\NotificationBundle\Sms
 */
class SmsRuTransport
{
    /**
     * @var
     */
    public $username;

    /**
     * @var
     */
    public $password;

    /**
     * @var
     */
    public $sender;

    /**
     * @param string $recipient
     * @param $body
     */
    public function send($recipient = '', $body)
    {
        if ($recipient) {
            $to = preg_replace('/[^\D]/', '', $recipient);
            $to[0] = 7;

            file_get_contents(
                'http://sms.ru/sms/send?from='.urlencode(
                    $this->sender
                ).'&login='.$this->username.'&password='.$this->password.'&to='.$to.'&text='.urlencode($body)
            );
            ///$response = file_get_contents("http://sms.ru/sms/send?login=".$this->user."&password=".$this->pass."&to=".$to."&text=".urlencode($text));
            //var_dump($response);
        }
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param mixed $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

}

