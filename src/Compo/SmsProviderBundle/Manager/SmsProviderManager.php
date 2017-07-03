<?php

namespace Compo\SmsProviderBundle\Manager;

use Compo\SmsProviderBundle\Entity\SmsProvider;
use Compo\SmsProviderBundle\Provider\SmsRuProvider;
use Compo\SmsProviderBundle\Repository\SmsProviderRepository;
use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritDoc}
 */
class SmsProviderManager extends BaseEntityManager
{
    use ContainerAwareTrait;


    public $providers = array(
        'smsru' => "\\Compo\\SmsProviderBundle\\Provider\\SmsRuProvider"
    );


    public function getTypesChoices() {
        return array(
            'sms.ru' => 'smsru'
        );
    }


    public function getSmsProviderByAccountId($id) {
        $account = $this->find($id);

        $class = $this->providers[$account['type']];

        /** @var SmsRuProvider $provider */
        $provider = new $class;

        $provider->setAccount($account);

        return $provider;
    }
}