<?php
namespace sgoranov\Dendroid\Bootstrap\Form;

use sgoranov\Dendroid\Component\Form\Element;

class Select extends Element
{
    protected $label;
    protected $options = [];

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    public function render(\DOMNode $node): \DOMNode
    {
        if (!$node instanceof \DOMElement) {
            throw new \InvalidArgumentException('DOMElement expected');
        }

        $node->setAttribute('class', 'form-group');

        $dom = $node->ownerDocument;

        if (!is_null($this->label)) {
            $element = $dom->createElement('label');
            $element->setAttribute('for', $this->name);
            $element->textContent = $this->label;
            $node->insertBefore($element);
        }

        $element = $dom->createElement('select');
        $element->setAttribute('name', $this->getName());

        $currentValue = null;
        if ($this->getForm()->isSubmitted()) {
            $currentValue = $this->getData();
        }

        foreach ($this->options as $value => $option) {
            $optionElement = $dom->createElement('option', $option);
            $optionElement->setAttribute('value', $value);
            if (!is_null($currentValue) && $currentValue == $value) {
                $optionElement->setAttribute('selected', 'selected');
            }

            $element->appendChild($optionElement);
        }

        $element->setAttribute('id', $this->name);

        $class = [
            'form-control',
        ];

        if (count($this->getErrors()) > 0) {
            $class[] = 'is-invalid';
        }

        $element->setAttribute('class', implode(' ', $class));
        $node->insertBefore($element);

        if (count($this->getErrors()) > 0) {
            $element = $dom->createElement('div');
            $element->setAttribute('class', 'invalid-feedback');

            $counter = 1;
            foreach ($this->getErrors() as $error) {
                $element->insertBefore($dom->createTextNode($error));

                if ($counter < count($this->getErrors())) {
                    $element->insertBefore($dom->createElement('br'));
                }
                $counter++;
            }

            $node->insertBefore($element);
        }

        return $node;
    }
}