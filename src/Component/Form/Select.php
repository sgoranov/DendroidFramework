<?php
namespace sgoranov\Dendroid\Component\Form;

class Select extends Element
{
    protected $options = [];

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function render(\DOMNode $node): \DOMNode
    {
        if (!$node instanceof \DOMElement) {
            throw new \InvalidArgumentException('DOMElement expected');
        }

        $dom = $node->ownerDocument;

        // overwrite the name of the form field
        $node->setAttribute('name', $this->getName());

        $currentValue = $this->data;
        if ($this->getForm()->isSubmitted()) {
            $currentValue = $this->getData();
        }

        foreach ($this->options as $value => $option) {

            $optionElement = $dom->createElement('option', htmlspecialchars($option));
            $optionElement->setAttribute('value', $value);

            if (!is_null($currentValue) && $currentValue == $value) {
                $optionElement->setAttribute('selected', 'selected');
            }

            $node->appendChild($optionElement);
        }

        $node->setAttribute('id', $this->name);

        return $node;
    }
}