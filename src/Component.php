<?php
namespace sgoranov\Dendroid;

use sgoranov\Dendroid\Component\Application;

abstract class Component implements ComponentInterface
{
    const DEFAULT_EVENT_VALUE = 'spf-default-event';

    protected $eventsExecutablesStorage;
    protected $eventsDefinitions = [];

    abstract public function render(\DOMNode $node): \DOMNode;

    private function getValidEvents()
    {
        $validEvents = [];

        /** @var EventDefinition $eventDefinition */
        foreach ($this->getEventsDefinitions() as $eventDefinition) {
            $validEvents[] = $eventDefinition->getName();
        }

        return $validEvents;
    }

    protected function addEventDefinition(EventDefinition $eventDefinition)
    {
        $currentEventDefinitions = $this->getValidEvents();
        if (in_array($eventDefinition->getName(), $currentEventDefinitions)) {
            throw new \InvalidArgumentException('EventDefinition with the same name is already defined');
        }

        $this->eventsDefinitions[] = $eventDefinition;
    }

    public function getEventsDefinitions()
    {
        return $this->eventsDefinitions;
    }

    public function attach(string $event, callable $callback)
    {
        if (!in_array($event, $this->getValidEvents())) {
            throw new \InvalidArgumentException(sprintf("Invalid event: %s. The allowed events for %s are: %s",
                $event, static::class, implode(', ', $this->getValidEvents())));
        }

        $this->eventsExecutablesStorage[$event][] = $callback;
    }

    /**
     * Execute all or subset of events related to
     * the component
     *
     * @param array $events Subset of events to run
     */
    public function eventsHandler($events = [])
    {
        if (empty($events)) {
            $definitions = $this->getEventsDefinitions();
        } else {
            $diff = array_diff($events, $this->getValidEvents());
            if (!empty($diff)) {
                throw new \InvalidArgumentException('Invalid event passed: ' . implode(', ', $diff));
            }

            $definitions = [];
            foreach ($this->getEventsDefinitions() as $eventDefinition) { /** @var EventDefinition $eventDefinition */
                if (in_array($eventDefinition->getName(), $events)) {
                    $definitions[] = $eventDefinition;
                }
            }
        }

        // automatically attach methods from current class
        foreach ($definitions as $eventDefinition) { /** @var EventDefinition $eventDefinition */
            if (method_exists($this, $eventDefinition->getName())) {
                $this->attach($eventDefinition->getName(), array($this, $eventDefinition->getName()));
            }
        }

        // handle the events
        foreach ($definitions as $eventDefinition) { /** @var EventDefinition $eventDefinition */

            // check for attached callbacks first
            if (empty($this->eventsExecutablesStorage[$eventDefinition->getName()])) {
                continue;
            }

            $isTriggered = $eventDefinition->isTriggered();
            if (!is_bool($isTriggered)) {
                throw new \InvalidArgumentException('The EventDefinition::isTriggered() must return boolean');
            }

            if ($isTriggered) {
                foreach ($this->eventsExecutablesStorage[$eventDefinition->getName()] as $callback) {
                    // pass the component as parameter to the callback
                    call_user_func($callback, $this);
                }
            }
        }
    }

    protected function getDOMElementFromString(\DOMDocument $document, string $content)
    {
        $dom = new \DOMDocument();

        // disable HTML errors/warnings
        $isLoaded = @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

        if (!$isLoaded) {
            throw new \InvalidArgumentException("Unable to load the HTML");
        }

        return $document->importNode($dom->documentElement, true);
    }

    protected function redirect($url, $statusCode = 303)
    {
        header('Location: ' . $url, true, $statusCode);
        $app = Application::getApplication();
        $app->shutDown();
    }
}
