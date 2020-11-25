<?php
namespace sgoranov\Dendroid\Component\Form;

use sgoranov\Dendroid\Component\Form as Form;
use sgoranov\Dendroid\ComponentInterface;

interface ElementInterface extends ComponentInterface
{
    public function setErrors(array $errors);
    public function getErrors(): array;
    public function getName();
    public function setData($data);
    public function getData();
    public function setForm(Form $form);
    public function getForm(): Form;
    public function setValidator(ValidatorInterface $validator);
    public function getValidator();
    public function hasChanged();
}