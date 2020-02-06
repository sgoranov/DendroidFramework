<?php
namespace sgoranov\Dendroid\DependencyInjection;

interface ContainerInterface
{
    public function call($callable, array $parameters = []);
}