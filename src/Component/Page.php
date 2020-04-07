<?php
namespace sgoranov\Dendroid\Component;

use sgoranov\Dendroid\ComponentContainer;
use sgoranov\Dendroid\Component;
use sgoranov\Dendroid\EventDefinition;

class Page extends ComponentContainer
{
    protected $routeParams = [];

    public function __construct()
    {
        $this->addEventDefinition(new EventDefinition('onInit', function () {
            return true;
        }));

        $this->addEventDefinition(new EventDefinition('onLoad', function () {
            return true;
        }));
    }

    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    public function setRouteParams(array $params)
    {
        $this->routeParams = $params;
    }
}
