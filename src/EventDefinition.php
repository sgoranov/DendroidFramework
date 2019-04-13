<?php
namespace sgoranov\Dendroid;

class EventDefinition
{
    private $name;
    private $isTriggered;

    public function __construct(string $name, callable $isTriggeredCallback)
    {
        $this->name = $name;
        $this->isTriggered = $isTriggeredCallback;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isTriggered()
    {
        return call_user_func($this->isTriggered);
    }
}
