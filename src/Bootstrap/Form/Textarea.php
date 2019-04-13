<?php
namespace sgoranov\Dendroid\Bootstrap\Form;

class Textarea extends \sgoranov\Dendroid\Component\Form\Textarea
{
    protected $label;

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

        $element = $dom->createElement('textarea');
        $element->setAttribute('id', $this->name);

        $class = [
            'form-control',
        ];

        if (count($this->getErrors()) > 0) {
            $class[] = 'is-invalid';
        }

        $element->setAttribute('class', implode(' ', $class));
        $element = parent::render($element);
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