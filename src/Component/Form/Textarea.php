<?php
namespace sgoranov\Dendroid\Component\Form;

class Textarea extends Element
{
    public function render(\DOMNode $node): \DOMNode
    {
        if (!$node instanceof \DOMElement) {
            throw new \InvalidArgumentException('DOMElement expected');
        }

        // overwrite the name of the form field
        $node->setAttribute('name', $this->name);

        if ($this->form->isSubmitted()) { // set submitted value
            $node->textContent = $this->getData();
        } elseif ($this->data !== '') { // set the predefined value before submission
            $node->textContent = $this->data;
        }

        return $node;
    }
}