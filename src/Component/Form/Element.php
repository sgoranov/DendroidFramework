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
    protected $attributes = [];

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
        // handle multiple values presented as array
        if ($this->isMultiValueElement()) {
            return str_replace('[]', '', $this->name);
        }

        return $this->name;
    }

    public function setData($data)
    {
        if ($this->isMultiValueElement()) {
            if (!is_array($data)) {
                throw new \InvalidArgumentException('Invalid data passed - array expected');
            }
        }

        $this->data = $data;
    }

    public function getData()
    {
        if ($this->form->isSubmitted()) {
            return $this->form->getData()[$this->getName()];
        }

        return $this->data;
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

    public function setId(string $id)
    {
        $this->attributes['id'] = $id;
    }

    public function setDisabled(bool $value)
    {
        if ($value) {
            $this->attributes['disabled'] = 'disabled';
        } else {
            unset($this->attributes['disabled']);
        }
    }

    public function setReadOnly(bool $value)
    {
        if ($value) {
            $this->attributes['readonly'] = 'readonly';
        } else {
            unset($this->attributes['readonly']);
        }
    }

    protected function isMultiValueElement()
    {
        return substr($this->getNameDefinition(), -2) === '[]';
    }

    protected function getNameDefinition()
    {
        return $this->name;
    }

    protected function getDataToRender()
    {
        if ($this->isMultiValueElement()) {

            $data = $this->getData();
            $response = array_shift($data);
            $this->setData($data);

            return $response;

        } else {

            return $this->getData();
        }
    }
}
