<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 12/10/17
 * Time: 16:09
 */

namespace Framework\Twig;


use Framework\Middleware\CsrfMiddleware;

class CsrfTwigExtension extends \Twig_Extension {

    /**
     * @var CsrfMiddleware
     */
    private $csrfMiddlewarre;

    /**
     * CsrfTwigExtension constructor.
     * @param CsrfMiddleware $csrfMiddlewarre
     */
    public function __construct(CsrfMiddleware $csrfMiddlewarre) {
        $this->csrfMiddlewarre = $csrfMiddlewarre;
    }

    /**
     * @return array
     */
    public function getFunctions():array {
        return [
            new \Twig_SimpleFunction('csrf_input', [$this, 'csrfInput'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @return string
     */
    public function csrfInput(): string {
        return '<input type="hidden" name="' . $this->csrfMiddlewarre->getFormKey() . '" value="' . $this->csrfMiddlewarre->generateToken() . '"/>';
    }

}