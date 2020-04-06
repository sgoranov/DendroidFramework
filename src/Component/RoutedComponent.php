<?php
namespace sgoranov\Dendroid\Component;

interface RoutedComponent
{
    public function getRoutePath(): string;
}