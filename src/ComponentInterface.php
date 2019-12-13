<?php
namespace sgoranov\Dendroid;

interface ComponentInterface
{
    public function render(\DOMNode $node): \DOMNode;
    public function getEventsDefinitions();
    public function attach(string $event, callable $callback);
    public function eventsHandler($events = []);
}