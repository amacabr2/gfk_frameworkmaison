<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 14/10/17
 * Time: 14:25
 */

namespace Tests\Framework\Database;


class Demo {

    private $slug;

    public function setSlug($slug) {
        $this->slug = $slug . 'demo';
    }

    /**
     * @return mixed
     */
    public function getSlug() {
        return $this->slug;
    }

}