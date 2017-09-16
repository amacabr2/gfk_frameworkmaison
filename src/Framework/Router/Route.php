<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 14/09/17
 * Time: 11:30
 */

namespace Framework\Router;

/**
 * Class Route
 * Represent a match route
 * @package Framework\Router
 */
class Route {

    /**
     * @var string
     */
    private $name;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var array
     */
    private $parameters;

    /**
     * Route constructor.
     * @param string $name
     * @param string|callable $callback
     * @param array $parameters
     */
    public function __construct(string $name, $callback, array $parameters) {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return callable|string
     */
    public function getCallback() {
        return $this->callback;
    }

    /**
     * Retireve the URL parameters
     * @return array[]
     */
    public function getParams(): array {
        return $this->parameters;
    }

}