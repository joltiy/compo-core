<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Twig;

/**
 * Class ColorExtension.
 */
class ColorExtension extends \Twig_Extension
{
    /**
     * COLOR_PERCENT.
     */
    public const COLOR_PERCENT = 2.55;

    /**
     * @param $color
     * @param $percent
     *
     * @return string
     */
    public static function lighten($color, $percent)
    {
        $mode = '';
        $color = self::normalizeColor($color, $mode);
        for ($i = 0; $i <= 2; ++$i) {
            $color[$i] = round($color[$i] + (self::COLOR_PERCENT * $percent));
            if ($color[$i] > 255) {
                $color[$i] = 255;
            }
        }

        return self::resolveColor($color, $mode);
    }

    /**
     * @param      $color
     * @param null $mode
     *
     * @return array|mixed|string
     */
    protected static function normalizeColor($color, &$mode = null)
    {
        $color = mb_strtolower($color);
        if (false !== mb_strpos($color, 'rgb')) {
            $mode = 'rgb';
            $color = trim($color, 'rgba()');
            $color = str_replace(' ', '', $color);
            $color = explode(',', $color);
            $count = \count($color);

            for ($i = 0; $i <= $count; ++$i) {
                $color[$i] = (3 === $i) ? (float) $color[$i] : (int) $color[$i];
            }
        } else {
            $mode = 'hex';
            $color = str_replace('#', '', $color);
            if (3 === mb_strlen($color)) {
                $color = str_repeat($color[0], 2) . str_repeat($color[1], 2) . str_repeat($color[2], 2);
            }
            $color = [
                hexdec(mb_substr($color, 0, 2)),
                hexdec(mb_substr($color, 2, 2)),
                hexdec(mb_substr($color, 4, 2)),
            ];
        }

        return $color;
    }

    /**
     * @param        $color
     * @param string $mode
     *
     * @return string
     */
    protected static function resolveColor($color, $mode = 'hex')
    {
        switch ($mode) {
            case 'hex':
                $red = sprintf('%02x', $color[0]);
                $green = sprintf('%02x', $color[1]);
                $blue = sprintf('%02x', $color[2]);
                $color = '#' . $red . $green . $blue;
                break;
            case 'rgb':
                $colorStr = self::hasAlpha($color) ? 'rgba(' : 'rgb(';

                $colorStr .= '' . $color[0] . ',' . $color[1] . ',' . $color[2];

                $colorStr .= self::hasAlpha($color) ? ',' . number_format($color[3], 2, '.', ',') . ')' : ')';
                $color = $colorStr;
                break;
            default:
                $color = '';
        }

        return $color;
    }

    /**
     * @param $color
     *
     * @return bool
     */
    protected static function hasAlpha($color)
    {
        return \count($color) > 3;
    }

    /**
     * @param $color
     * @param $percent
     *
     * @return string
     */
    public static function darken($color, $percent)
    {
        $mode = '';
        $color = self::normalizeColor($color, $mode);
        for ($i = 0; $i <= 2; ++$i) {
            $color[$i] = round($color[$i] - (self::COLOR_PERCENT * $percent));
            if ($color[$i] < 0) {
                $color[$i] = 0;
            }
        }

        return self::resolveColor($color, $mode);
    }

    /**
     * @param      $color
     * @param null $set
     *
     * @return string
     */
    public static function red($color, $set = null)
    {
        $mode = '';
        $color = self::normalizeColor($color, $mode);
        if (null !== $set) {
            $newColor = [
                $set,
                $color[1],
                $color[2],
            ];
            if (self::hasAlpha($color)) {
                $newColor[3] = $color[3];
            }

            return self::resolveColor($newColor, $mode);
        }

        return $color[0];
    }

    /**
     * @param      $color
     * @param null $set
     *
     * @return string
     */
    public static function green($color, $set = null)
    {
        $mode = '';
        $color = self::normalizeColor($color, $mode);
        if (null !== $set) {
            $newColor = [
                $color[0],
                $set,
                $color[2],
            ];
            if (self::hasAlpha($color)) {
                $newColor[3] = $color[3];
            }

            return self::resolveColor($newColor, $mode);
        }

        return $color[1];
    }

    /**
     * @param      $color
     * @param null $set
     *
     * @return string
     */
    public static function blue($color, $set = null)
    {
        $mode = '';
        $color = self::normalizeColor($color, $mode);
        if (null !== $set) {
            $newColor = [
                $color[0],
                $color[1],
                $set,
            ];
            if (self::hasAlpha($color)) {
                $newColor[3] = $color[3];
            }

            return self::resolveColor($newColor, $mode);
        }

        return $color[2];
    }

    /**
     * @param      $color
     * @param null $set
     *
     * @return float|string
     */
    public static function alpha($color, $set = null)
    {
        $mode = '';
        $color = self::normalizeColor($color, $mode);
        if (null !== $set) {
            $color[3] = ($set / 100);

            return self::resolveColor($color, 'rgb');
        }

        return self::hasAlpha($color) ? $color[3] : (float) 1;
    }

    /**
     * @param $colors
     *
     * @return mixed|string
     */
    public static function mix($colors)
    {
        if (!\is_array($colors)) {
            $colors = \func_get_args();
        }
        if (empty($colors)) {
            return '';
        }
        if (1 === \count($colors)) {
            return $colors[0];
        }

        $amountColors = \count($colors);
        foreach ($colors as $key => $val) {
            $colors[$key] = self::normalizeColor($val);
        }
        $totalRed = 0;
        $totalGreen = 0;
        $totalBlue = 0;
        foreach ($colors as $color) {
            $totalRed += $color[0];
            $totalGreen += $color[1];
            $totalBlue += $color[2];
        }
        $color = [
            round($totalRed / $amountColors),
            round($totalGreen / $amountColors),
            round($totalBlue / $amountColors),
        ];

        return self::resolveColor($color);
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('color_lighten', '\Compo\CoreBundle\Twig\ColorExtension::lighten'),
            new \Twig_SimpleFunction('color_darken', '\Compo\CoreBundle\Twig\ColorExtension::darken'),
            new \Twig_SimpleFunction('color_red', '\Compo\CoreBundle\Twig\ColorExtension::red'),
            new \Twig_SimpleFunction('color_green', '\Compo\CoreBundle\Twig\ColorExtension::green'),
            new \Twig_SimpleFunction('color_blue', '\Compo\CoreBundle\Twig\ColorExtension::blue'),
            new \Twig_SimpleFunction('color_alpha', '\Compo\CoreBundle\Twig\ColorExtension::alpha'),
            new \Twig_SimpleFunction('color_mix', '\Compo\CoreBundle\Twig\ColorExtension::mix'),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'color_extension';
    }
}
