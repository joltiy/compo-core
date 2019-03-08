<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Factory;

use Sylius\Bundle\ThemeBundle\Model\ThemeScreenshot;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ThemeScreenshotFactory implements ThemeScreenshotFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $data)
    {
        if (!\array_key_exists('path', $data)) {
            throw new \InvalidArgumentException('Screenshot path is required.');
        }

        $themeScreenshot = new ThemeScreenshot($data['path']);

        if (isset($data['title'])) {
            $themeScreenshot->setTitle($data['title']);
        }

        if (isset($data['description'])) {
            $themeScreenshot->setDescription($data['description']);
        }

        return $themeScreenshot;
    }
}
