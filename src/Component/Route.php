<?php
namespace sgoranov\Dendroid\Component;

use sgoranov\Dendroid\Component;
use sgoranov\Dendroid\ComponentContainerInterface;

class Route extends Component implements ComponentContainerInterface
{
    protected $pathPrefix;
    protected $routes = [];

    public function addPage(Page $page, string $path)
    {
        $this->routes[$path] = $page;
    }

    public function setPathPrefix(string $path)
    {
        $this->pathPrefix = $path;
    }

    public function createUrl(string $page, array $parameters = [])
    {
        foreach ($this->routes as $route => $pageObject) {
            if ($pageObject instanceof $page) {

                $result = preg_replace("/\([^)]+\)/",
                    "%s",
                    $this->pathPrefix ?  $this->pathPrefix . $route : $route,
                    -1,
                    $numberOfParameters
                );

                if ($numberOfParameters === count($parameters)) {

                    // escape the parameters
                    array_walk($parameters, function ($string) {
                        htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
                    });

                    return vsprintf($result, $parameters);
                }
            }
        }

        throw new \InvalidArgumentException('The route definition is not found');
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function addSubRoute(Route $route, string $path)
    {
        foreach ($route->getRoutes() as $routePath => $page) {
            $this->addPage($page, $path . $routePath);
        }
        $route->setPathPrefix($path);
    }

    public function getPageByRoute(string $uri = null)
    {
        if (is_null($uri)) {
            $uri = $_SERVER['REQUEST_URI'];
        }

        /**
         * @var string $path
         * @var Page $page
         */
        foreach ($this->routes as $path => $page) {
            if (preg_match("~^$path$~i", $uri, $matches)) {

                $page->setRouteParams($matches);

                return $page;
            }
        }

        throw new \InvalidArgumentException('The route definition does not exists');
    }

    public function render(\DOMNode $node): \DOMNode
    {
        $page = $this->getPageByRoute();

        return $page->render($node);
    }

    public function getComponents(): array
    {
        $page = $this->getPageByRoute();

        return $page->getComponents();
    }

    public function getEventsDefinitions(): array
    {
        $page = $this->getPageByRoute();

        return $page->getEventsDefinitions();
    }

    public function eventsHandler($events = [])
    {
        $page = $this->getPageByRoute();

        $page->eventsHandler($events);
    }
}