<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 26/09/17
 * Time: 14:37
 */

namespace Framework\Twig;


use Framework\Session\FlashService;

class FlashExtension extends \Twig_Extension {

    /**
     * @var FlashService
     */
    private $flashService;

    public function __construct(FlashService $flashService) {
        $this->flashService = $flashService;
    }

    /**
     * @return array
     */
    public function getFunctions(): array {
        return [
            new \Twig_SimpleFunction('flash', [$this, 'getFlash'])
        ];
    }

    /**
     * @param $type
     * @return null|string
     */
    public function getFlash($type): ?string {
        return $this->flashService->get($type);
    }

}