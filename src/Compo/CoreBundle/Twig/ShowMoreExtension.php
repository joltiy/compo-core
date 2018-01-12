<?php
/**
 * Created by PhpStorm.
 * User: jivoy1988
 * Date: 01.11.16
 * Time: 6:54.
 */

namespace Compo\CoreBundle\Twig;

use Twig_Extension;
use Twig_SimpleFilter;

/**
 * Class ShowMoreExtension.
 */
class ShowMoreExtension extends Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            'show_more' => new Twig_SimpleFilter('show_more', [$this, 'showMoreFilter']),
        ];
    }

    /**
     * @param $text
     *
     * @return mixed|string
     */
    public function showMoreFilter($text)
    {
        // <!--more--></div>
        $text = str_replace('<!--more--></div>', '</div><!--more-->', $text);

        $text_array = explode('<!--more-->', $text, 2);

        if (2 === count($text_array) && $text_array[0] && $text_array[1]) {
            $result = '<div class="show-more-block">';

            $result .= '<div class="show-more-teaser">' . $text_array[0] . '</div>';
            $result .= '<div class="show-more-complete">' . $text_array[1] . '</div>';
            $result .= '<div class="show-more-button"><span class="link">Подробнее...</span></div>';

            $result .= '</div>';

            return $result;
        }

        return $text;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'show_more_extension';
    }
}
