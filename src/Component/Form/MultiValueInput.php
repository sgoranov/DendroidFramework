<?php
namespace sgoranov\Dendroid\Component\Form;

class MultiValueInput extends Input
{
    protected $data = [];

    public function setData($data)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid data passed');
        }

        if ($this->form && $this->form->isSubmitted()) {
            $this->submittedData = $data;
        } else {
            $this->data = $data;
        }
    }

    protected function getDataToRender()
    {
        $data = $this->getData();
        $response = array_shift($data);
        $this->setData($data);

        return $response;
    }

    public function render(\DOMNode $node, $name = null): \DOMNode
    {
        $document = $node->ownerDocument;
        $fragment = $document->createDocumentFragment();

        $elementsToRender = count($this->getData());
        if ($elementsToRender > 0) {

            for ($i = 0; $i < $elementsToRender; $i++) {
                $newNode = parent::render($node, $this->getName()  . '[]');
                $fragment->appendChild($newNode->cloneNode(true));
            }
        }

        return $fragment;
    }
}