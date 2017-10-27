<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 15/10/17
 * Time: 10:47
 */

namespace App\Blog;


use Framework\Upload;

class PostUpload extends Upload {

    protected $path = 'public/uploads/posts';

    protected $formats = [
        'thumb' => [320, 180]
    ];

}