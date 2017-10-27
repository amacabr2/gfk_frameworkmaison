<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 13/09/17
 * Time: 09:59
 */

namespace Framework;


use DI\ContainerBuilder;
use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\FilesystemCache;
use Framework\Middleware\RouterPrefixedMiddleware;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App implements DelegateInterface {

    /**
     * @var array
     */
    private $modules = [];

    /**
     * @var string
     */
    private $definition;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string[]
     */
    private $middlewares;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * App constructor.
     * @param string $definition
     */
    public function __construct(string $definition) {
        $this->definition = $definition;
    }

    /**
     * @param string $module
     * @return App
     */
    public function addModule(string $module): self {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * @param string|null $routePrefix
     * @param string $middleware
     * @return App
     */
    public function pipe(string $middleware, ?string $routePrefix = null): self {
        if ($routePrefix === null) {
            $this->middlewares[] = $middleware;
        } else {
            $this->middlewares[] = new RouterPrefixedMiddleware($this->getContainer(), $routePrefix, $middleware);
        }
        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request): ResponseInterface {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            throw new \Exception("Aucun middleware n'a intercepté cette requète.");
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'process']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function run(ServerRequestInterface $request): ResponseInterface {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->process($request);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $env = getenv('ENV') ?: 'production';
            if ($env == 'production') {
                $builder->setDefinitionCache(new FilesystemCache('tmp/di'));
                $builder->writeProxiesToFile(true, 'tmp/proxies');
            }
            $builder->addDefinitions($this->definition);
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $this->container = $builder->build();
        }
        return $this->container;
    }

    /**
     * @return mixed
     */
    private function getMiddleware() {
        if (array_key_exists($this->index, $this->middlewares)) {
            if (is_string($this->middlewares[$this->index])) {
                $middleware =  $this->container->get($this->middlewares[$this->index]);
            } else {
                $middleware = $this->middlewares[$this->index];
            }
            $this->index++;
            return $middleware;
        }
        return null;
    }

    /**
     * @return array
     */
    public function getModules(): array {
        return $this->modules;
    }

}