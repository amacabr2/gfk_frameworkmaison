<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 19/09/17
 * Time: 12:29
 */

namespace App\Blog\Entity;


class Post {

    public $id;

    public $name;

    public $slug;

    public $content;

    public $createdAt;

    public $updatedAt;

    public $categoryName;

    public $image;

    /**
     * @param $datetime
     */
    public function setCreatedAt($datetime) {
        if (is_string($datetime)) {
            $this->createdAt = new \DateTime($datetime);
        }
    }

    /**
     * @param $datetime
     */
    public function setUpdatedAt($datetime) {
        if (is_string($datetime)) {
            $this->updatedAt = new \DateTime($datetime);
        }
    }

    public function getThumb() {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
        return '/uploads/posts/' . $filename . '_thumb.' . $extension;
    }

    public function getImageUrl() {
        return '/uploads/posts/' . $this->image;
    }
}