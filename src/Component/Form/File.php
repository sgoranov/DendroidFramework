<?php
namespace sgoranov\Dendroid\Component\Form;

class File extends Element
{
    public function setData(string $data)
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

        $node->setAttribute('name', $this->name);
        $node->setAttribute('type', 'file');

        return $node;
    }
}
