<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 19/09/17
 * Time: 12:45
 */

namespace Framework\Twig;


class TimeExtension extends \Twig_Extension {

    /**
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters(): array {
        return [
            new \Twig_SimpleFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    public function ago(\DateTime $date, string $format = 'd/m/Y H:i') {
        return '<span class="timeago" datetime="' . $date->format(\DateTime::ISO8601) .'">' . $date->format($format) . '</span>';
    }

}