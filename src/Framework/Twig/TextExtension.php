<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 19/09/17
 * Time: 11:26
 */

namespace Framework\Twig;


class TextExtension extends \Twig_Extension {

    /**
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters(): array {
        return [
            new \Twig_SimpleFilter('excerpt', [$this, 'excerpt'])
        ];
    }

    /**
     * @param string $content
     * @param int $maxLength
     * @return string
     */
    public function excerpt(?string $content, int $maxLength = 100): string {
        if (is_null($content)) {
            return '';
        }
        if (mb_strlen($content) > $maxLength) {
            $excerpt = mb_substr($content, 0, $maxLength);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';

        }
        return $content;
    }

}