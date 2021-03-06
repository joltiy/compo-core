<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pix\SortableBehaviorBundle\Twig;

use Pix\SortableBehaviorBundle\Services\PositionHandler;

/**
 * Description of ObjectPositionExtension.
 *
 * @author Volker von Hoesslin <volker.von.hoesslin@empora.com>
 */
class ObjectPositionExtension extends \Twig_Extension
{
    const NAME = 'sortableObjectPosition';

    /**
     * PositionHandler.
     */
    private $positionService;

    public function __construct(PositionHandler $positionService)
    {
        $this->positionService = $positionService;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(self::NAME,
                function ($entity) {
                    $getter = sprintf('get%s', ucfirst($this->positionService->getPositionFieldByEntity($entity)));

                    return $entity->{$getter}();
                }
            ),
        ];
    }
}
