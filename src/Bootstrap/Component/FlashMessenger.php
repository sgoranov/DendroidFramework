<?php
namespace sgoranov\Dendroid\Bootstrap\Component;

use sgoranov\Dendroid\Component;
use sgoranov\Dendroid\Component\Application;
use sgoranov\Dendroid\EventDefinition;

class FlashMessenger extends Component
{
    public function __construct()
    {
        if (!isset($_SESSION['flash_messages']['success'])) {
            $_SESSION['flash_messages']['success'] = [];
        }

        if (!isset($_SESSION['new_flash_messages']['success'])) {
            $_SESSION['new_flash_messages']['success'] = [];
        }

        $this->addEventDefinition(new EventDefinition('initOnShutDown', function () {
            return true;
        }));
    }

    public function initOnShutDown() {
        $app = Application::getApplication();
        $app->attach('onShutDown', function () {
            $_SESSION['flash_messages']['success'] = $_SESSION['new_flash_messages']['success'];
            $_SESSION['new_flash_messages']['success'] = [];
        });
    }

    public function addSuccess(string $message)
    {
        $_SESSION['new_flash_messages']['success'][] = $message;
    }

    public function render(\DOMNode $node): \DOMNode
    {
        $success = $_SESSION['flash_messages']['success'];

        // print the messages
        if (!empty($success)) {
            $html = '';
            foreach ($success as $message) {
                $html .= '<div class="alert alert-success" role="alert">';
                $html .= $message;
                $html .= '</div>';
            }

            $newNode = $this->getDOMElementFromString($node->ownerDocument, $html);
            $node->appendChild($newNode);
        }

        return $node;
    }
}
