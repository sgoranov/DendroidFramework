<?php
namespace sgoranov\Dendroid\Bootstrap;

use sgoranov\Dendroid\Component\Form;
use sgoranov\Dendroid\Component\Form\Element;
use sgoranov\Dendroid\Component\Form\ElementInterface;
use sgoranov\Dendroid\Component\Form\File;
use sgoranov\Dendroid\Component\Form\Select;
use sgoranov\Dendroid\Component\Form\Textarea;
use sgoranov\Dendroid\Component\Form\ValidatorInterface;

class FormElement implements ElementInterface
{
    protected $label;

    protected Element $element;

    public function __construct(Element $element)
    {
        $this->element = $element;
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
            $element->setAttribute('for', $this->element->getName());
            $element->textContent = $this->label;
            $node->insertBefore($element);
        }

        // create the propper node, where to place the element
        if ($this->element instanceof File) {

            $element = $dom->createElement('input');
            $class = ['form-control-file'];

        } elseif ($this->element instanceof Textarea)  {

            $element = $dom->createElement('textarea');
            $class = ['form-control'];

        } elseif ($this->element instanceof Select) {

            $element = $dom->createElement('select');
            $class = ['form-control'];

        } else {

            $element = $dom->createElement('input');
            $class = ['form-control'];
        }

        $element->setAttribute('id', $this->element->getName());

        if (count($this->getErrors()) > 0) {
            $class[] = 'is-invalid';
        }

        $element->setAttribute('class', implode(' ', $class));
        $element =  $this->element->render($element);
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

    public function setErrors(array $errors)
    {
        return $this->element->setErrors($errors);
    }

    public function getErrors(): array
    {
        return $this->element->getErrors();
    }

    public function getName()
    {
        return $this->element->getName();
    }

    public function setData(string $data)
    {
        return $this->element->setData($data);
    }

    public function getData()
    {
        return $this->element->getData();
    }

    public function setForm(Form $form)
    {
        return $this->element->setForm($form);
    }

    public function getForm(): Form
    {
        return $this->element->getForm();
    }

    public function setValidator(ValidatorInterface $validator)
    {
        return $this->element->setValidator($validator);
    }

    public function getValidator()
    {
        return $this->element->getValidator();
    }

    public function hasChanged()
    {
        return $this->element->hasChanged();
    }

    public function getEventsDefinitions()
    {
        return $this->element->getEventsDefinitions();
    }

    public function attach(string $event, callable $callback)
    {
        return $this->element->attach($event, $callback);
    }

    public function eventsHandler($events = [])
    {
        return $this->element->eventsHandler($events);
    }
}