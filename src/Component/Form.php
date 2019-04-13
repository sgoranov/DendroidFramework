<?php
namespace sgoranov\Dendroid\Component;

use sgoranov\Dendroid\ComponentContainer;
use sgoranov\Dendroid\Component;
use sgoranov\Dendroid\Component\Form\Element;
use sgoranov\Dendroid\EventDefinition;

class Form extends ComponentContainer
{
    protected $onSubmit;
    protected $id;
    protected $method;
    protected $csrfEnabled;
    protected $errors = [];

    public function __construct(string $id, string $method = 'post', bool $csrfEnabled = true)
    {
        if ($method !== 'post' && $method !== 'get') {
            throw new \InvalidArgumentException('Invalid method passed');
        }

        $this->addEventDefinition(new EventDefinition('onSubmit', [$this, 'isSubmitted']));

        $this->id = $id;
        $this->method = $method;
        $this->csrfEnabled = $csrfEnabled;
    }

    public function addComponent($ref, Component $component)
    {
        /** @var Element $component */
        if (!$component instanceof Element) {
            throw new \InvalidArgumentException('Invalid component passed');
        }

        $component->setForm($this);

        parent::addComponent($ref, $component);
    }

    public function getId()
    {
        return $this->id;
    }

    public function addError(string $error)
    {
        $this->errors[] = $error;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function render(\DOMNode $parent): \DOMNode
    {
        /** @var \DOMElement $parent */
        if (!$parent instanceof \DOMElement) {
            throw new \InvalidArgumentException('DOMElement expected');
        }

        $parent = parent::render($parent);
        $parent->setAttribute('id', $this->id);
        $parent->setAttribute('method', $this->method);

        $dom = $parent->ownerDocument;
        $hidden = $dom->createElement('input');
        $hidden->setAttribute('type', 'hidden');
        $hidden->setAttribute('name', 'event');
        $hidden->setAttribute('value', $this->id);
        $parent->insertBefore($hidden);

        if ($this->csrfEnabled) {
            $hidden = $dom->createElement('input');
            $hidden->setAttribute('type', 'hidden');
            $hidden->setAttribute('name', 'csrf_token');
            $hidden->setAttribute('value', $this->generateCSRFToken());
            $parent->insertBefore($hidden);
        }

        return $parent;
    }

    public function isSubmitted()
    {
        return isset($_POST['event']) && $_POST['event'] === $this->getId();
    }

    public function isValid()
    {
        if (!$this->isSubmitted()) {
            return true;
        }

        $isValid = true;

        // CSRF validation
        if ($this->csrfEnabled) {
            if ($this->method === 'get') {
                $submittedToken = $_GET['csrf_token'];
            } else {
                $submittedToken = $_POST['csrf_token'];
            }

            if ($this->getCSRFToken() !== $submittedToken) {
                $isValid = false;
                $this->addError('CSRF validation failed');
            }
        }

        $formData = $this->getData();

        /** @var Element $element */
        foreach ($this->components as $element) {
            $validator = $element->getValidator();

            if (!is_null($validator)) {
                $data = $formData[$element->getName()];

                if (!$validator->isValid($data)) {
                    $element->setErrors($validator->getErrors());
                    $isValid = false;
                }
            }
        }

        return $isValid;
    }

    public function setData(array $data)
    {
        /** @var Element $component */
        foreach ($this->getComponents() as $component) {
            $name = $component->getName();

            if (isset($data[$name])) {
                $component->setData($data[$name]);
            }
        }
    }

    public function getData()
    {
        if (!$this->isSubmitted()) {
            throw new \InvalidArgumentException('Form is not submitted.');
        }

        $result = [];

        /** @var Element $component */
        foreach ($this->getComponents() as $component) {
            $name = $component->getName();

            if ($this->method === 'get') {
                $result[$name] = $_GET[$name];
            } else {
                $result[$name] = $_POST[$name];
            }
        }

        return $result;
    }

    protected function getCSRFToken()
    {
        return $_SESSION['form_' . $this->getId()]['csrf_token'];
    }

    protected function generateCSRFToken()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['form_' . $this->getId()]['csrf_token'] = $token;

        return $token;
    }
}