<?php
namespace sgoranov\Dendroid\Component\Form;

class Input extends Element
{
    protected $type = 'text';

    public function setType($type = 'text')
    {
        $this->type = $type;
    }

    public function render(\DOMNode $node): \DOMNode
    {
        if (!$node instanceof \DOMElement) {
            throw new \InvalidArgumentException('DOMElement expected');
        }

        // overwrite the name of the form field
        $node->setAttribute('name', $this->name);

        if ($this->form->isSubmitted()) { // set submitted value
            $node->setAttribute('value', $this->getData());
        } elseif ($this->data !== '') { // set the predefined value before submission
            $node->setAttribute('value', $this->data);
        }

        // set type of the input, type="text" by default
        $node->setAttribute('type', $this->type);

        return $node;
    }
}