<?php
namespace sgoranov\Dendroid\Component;

use sgoranov\Dendroid\Component;
use sgoranov\Dendroid\ComponentContainerInterface;
use sgoranov\Dendroid\Component\Exception\RouteNotFoundException;

class Route extends Component implements ComponentContainerInterface
{
    protected $pathPrefix;
    protected $routes = [];

    public function addRoutedComponent(RoutedComponent $component)
    {
        $this->addPage($component, $component->getRoutePath());
    }

    public function addPage(Page $page, string $path)
    {
        if (in_array($page, $this->routes)) {

            throw new \InvalidArgumentException(sprintf(
                'There is existing route definition "%s" for %s',
                array_search($page, $this->routes),
                get_class($page))
            );
        }

        $this->routes[$path] = $page;
    }

    public function setPathPrefix(string $path)
    {
        $this->pathPrefix = $path;
    }

    public function createUrl(string $page, array $parameters = [])
    {
        foreach ($this->routes as $route => $pageObject) {
            if (get_class($pageObject) === $page) {

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

        throw new \InvalidArgumentException(
            sprintf("The route definition (%s) is not found or number of parameters doesn't match", $page));
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

            $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
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

        throw new RouteNotFoundException('The route definition does not exists');
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