<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Exception;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
final class ParameterNotFoundException extends \InvalidArgumentException
{
    /**
     * @param string          $parameter
     * @param int             $code
     * @param \Exception|null $previousException
     */
    public function __construct($parameter, $code = 0, \Exception $previousException = null)
    {
        $message = sprintf('Parameter with name "%s" does not exist.', $parameter);

        parent::__construct($message, $code, $previousException);
    }
}
