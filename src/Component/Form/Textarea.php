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
        $node->setAttribute('name', $this->getNameDefinition());

        // set all additional attributes
        foreach ($this->attributes as $key => $value) {
            $node->setAttribute($key, $value);
        }

        $node->textContent = $this->getDataToRender();

        return $node;
    }
}