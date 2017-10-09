<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 09/10/17
 * Time: 11:10
 */

namespace App\Admin;


interface AdminWidgetInterface {

    /**
     * @return string
     */
    public function render(): string;

    public function renderMenu(): string;

}