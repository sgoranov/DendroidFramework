<?php
namespace sgoranov\Dendroid\Component;


use sgoranov\Dendroid\Component\Form\Element;

class Button extends Element
{
    protected $name;
    protected $value;
    protected $onClick;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function render(\DOMNode $node): \DOMNode
    {
        if (!$node instanceof \DOMElement) {
            throw new \InvalidArgumentException('DOMElement expected');
        }

        $node->setAttribute('form', self::DEFAULT_EVENT_VALUE);
        if ($this->form instanceof Form) {
            $node->setAttribute('form', $this->form->getId());
        }

        $node->setAttribute('name', $this->name);
        $node->setAttribute('value', $this->value);
        $node->setAttribute('type', 'submit');

        return $node;
    }

    public function getEvents(): array
    {
        return [
            'onClick' => function () {
                return $this->isClicked();
            },
        ];
    }

    public function isClicked(): bool
    {
        $form = $this->getForm();
        if ($form !== false) {

            /** @var Form $form */
            return isset($_POST['event']) &&
                $_POST['event'] === $form->getId() &&
                isset($_POST[$this->name]) &&
                $_POST[$this->name] == $this->value;

        } else {

            return isset($_POST['event']) &&
                $_POST['event'] === self::DEFAULT_EVENT_VALUE &&
                isset($_POST[$this->name]) &&
                $_POST[$this->name] == $this->value;
        }
    }
}
