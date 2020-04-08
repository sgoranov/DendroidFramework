<?php
namespace sgoranov\Dendroid\Bootstrap\Component;

use sgoranov\Dendroid\Component;
use sgoranov\Dendroid\Component\Application;
use sgoranov\Dendroid\EventDefinition;

class FlashMessenger extends Component
{
    private $types = ['success', 'error'];

    private $classMappings = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
    ];

    public function __construct()
    {
        // init session variables
        foreach ($this->types as $type) {
            if (!isset($_SESSION['flash_messages'][$type])) {
                $_SESSION['flash_messages'][$type] = [];
            }

            if (!isset($_SESSION['new_flash_messages'][$type])) {
                $_SESSION['new_flash_messages'][$type] = [];
            }
        }

        $this->addEventDefinition(new EventDefinition('initOnShutDown', function () {
            return true;
        }));
    }

    public function initOnShutDown() {
        $app = Application::getApplication();
        $app->attach('onShutDown', function () {

            // move "new" to "current" and reset the "new" container
            foreach ($this->types as $type) {

                $_SESSION['flash_messages'][$type] = $_SESSION['new_flash_messages'][$type];
                $_SESSION['new_flash_messages'][$type] = [];

            }
        });
    }

    public function addSuccess(string $message)
    {
        $_SESSION['new_flash_messages']['success'][] = $message;
    }

    public function addError(string $message)
    {
        $_SESSION['new_flash_messages']['error'][] = $message;
    }

    public function render(\DOMNode $node): \DOMNode
    {
        $html = '';

        foreach ($this->types as $type) {

            $messages = $_SESSION['flash_messages'][$type];

            if (!empty($messages)) {

                $class = $this->classMappings[$type];

                foreach ($messages as $message) {

$html .= <<<EOF

<div class="alert $class" role="alert">
    $message
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

EOF;
                }
            }

        }

        if (!empty($html)) {

            $html = "<div>$html</div>";

            $newNode = $this->getDOMElementFromString($node->ownerDocument, $html);
            $node->appendChild($newNode);
        }

        return $node;
    }
}
