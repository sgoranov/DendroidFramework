<?php
namespace sgoranov\Dendroid\Component\Form;

class File extends Element
{
    public function setData($data)
    {
        throw new \Exception('Operation not supported');
    }

    public function getData()
    {
        return $this->form->getData()[$this->name];
    }

    public function render(\DOMNode $node): \DOMNode
    {
        if (!$node instanceof \DOMElement) {
            throw new \InvalidArgumentException('DOMElement expected');
        }

        // overwrite the name of the form field
        $node->setAttribute('name', $this->getName());

        // set all additional attributes
        foreach ($this->attributes as $key => $value) {
            $node->setAttribute($key, $value);
        }

        $node->setAttribute('type', 'file');

        return $node;
    }
}
