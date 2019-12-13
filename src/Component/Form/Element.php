<?php
namespace sgoranov\Dendroid\Component\Form;

use sgoranov\Dendroid\Component;
use sgoranov\Dendroid\Component\Form as Form;
use sgoranov\Dendroid\EventDefinition;

abstract class Element extends Component implements ElementInterface
{
    /** @var  Form */
    protected $form;
    protected $name;
    protected $data = '';
    protected $errors = [];
    protected $validator;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->addEventDefinition(new EventDefinition('onChange', [$this, 'hasChanged']));
    }

    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setData(string $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->form->getData()[$this->name];
    }

    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function getValidator()
    {
        return $this->validator;
    }

    public function hasChanged()
    {
        if ($this->form->isSubmitted() && $this->data !== $this->getData()) {
            return true;
        }

        return false;
    }
}
