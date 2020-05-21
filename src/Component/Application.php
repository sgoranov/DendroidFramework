<?php
namespace sgoranov\Dendroid\Component;

use sgoranov\Dendroid\Component;
use sgoranov\Dendroid\ComponentContainerInterface;
use sgoranov\Dendroid\DependencyInjection\ContainerInterface;
use sgoranov\Dendroid\EventDefinition;

class Application extends Page
{
    /** @var Application */
    protected static $app; // started application

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->addEventDefinition(new EventDefinition('onStart', function() {
            return true;
        }));

        $this->addEventDefinition(new EventDefinition('onShutDown', function() {
            return true;
        }));

        $this->container = $container;
    }

    public static function getApplication()
    {
        if (is_null(static::$app)) {
            throw new \InvalidArgumentException('There is no running application');
        }

        return static::$app;
    }

    public function getContainer()
    {
        return $this->container;
    }

    protected function getDOMFromString($content)
    {
        $dom = new \DOMDocument();

        $isLoaded = @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

        if (!$isLoaded) {
            throw new \InvalidArgumentException("Unable to load the HTML");
        }

        return $dom;
    }

    public function start()
    {
        if (!is_null(static::$app)) {
            throw new \InvalidArgumentException('There is another application already running');
        }

        static::$app = $this;

        $this->eventsHandler(['onStart']);

        // handle the events before rendering
        $this->recursiveEventsHandler($this->getComponents());

        // start the app rendering
        $dom = $this->getDOMFromString($this->getHtml());

        // create the default event form
        $result = $dom->getElementsByTagName('body');
        if ($result->length !== 1) {
            throw new \InvalidArgumentException('Missing body tag');
        }

        $element = $dom->createElement('form');
        $element->setAttribute('method', 'post');
        $element->setAttribute('id', 'spf-default-event');

        $hidden = $dom->createElement('input');
        $hidden->setAttribute('type', 'hidden');
        $hidden->setAttribute('name', 'event');
        $hidden->setAttribute('value', 'spf-default-event');
        $element->insertBefore($hidden);

        $body = $result->item(0);
        $body->insertBefore($element);

        // continue rendering
        $dom = $this->render($dom);
        echo $dom->saveHTML();
    }

    public function shutDown()
    {
        $this->eventsHandler(['onShutDown']);
        static::$app = null;
        exit();
    }

    protected function recursiveEventsHandler($components)
    {
        // handle sub components
        foreach ($components as $component) { /** @var Component $component */
            if ($component instanceof ComponentContainerInterface) {
                $component->eventsHandler();
                $this->recursiveEventsHandler($component->getComponents());
            } else {
                $component->eventsHandler();
            }
        }
    }
}