<?php

namespace Compo\SmsProviderBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\SmsProviderBundle\Provider\SmsRuProvider;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritDoc}
 */
class SmsProviderManager extends BaseEntityManager
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    public $providers = array(
        'smsru' => "\\Compo\\SmsProviderBundle\\Provider\\SmsRuProvider"
    );

    /**
     * @return array
     */
    public function getTypesChoices()
    {
        return array(
            'sms.ru' => 'smsru'
        );
    }

    /**
     * @param $id
     * @return SmsRuProvider
     */
    public function getSmsProviderByAccountId($id)
    {
        $account = $this->find($id);

        $class = $this->providers[$account['type']];

        /** @var SmsRuProvider $provider */
        $provider = new $class;

        $provider->setAccount($account);

        return $provider;
    }
}