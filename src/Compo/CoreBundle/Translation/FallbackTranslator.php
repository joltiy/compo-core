<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Translation;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Class FallbackTranslator.
 */
class FallbackTranslator extends Translator
{
    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        if (null === $locale) {
            $locale = $this->getLocale();
        }

        if (null === $domain) {
            $domain = 'messages';
        }

        if (!isset($this->catalogues[$locale])) {
            $this->loadCatalogue($locale);
        }

        // Change translation domain to 'messages' if a translation can't be found in the
        // current domain
        if ('messages' !== $domain && false === $this->catalogues[$locale]->has((string) $id, $domain)) {
            $domain = 'messages';
        }

        return strtr($this->catalogues[$locale]->get((string) $id, $domain), $parameters);
    }
}
