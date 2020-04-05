<?php
namespace sgoranov\Dendroid\Validator;

use sgoranov\Dendroid\Component\Form\ValidatorInterface;

class RegularExpression implements ValidatorInterface
{
    private string $regExpression;
    private string $msg;
    private array $errors = [];

    public function __construct(string $regExpression, string $msg)
    {
        $this->regExpression = $regExpression;
        $this->msg = $msg;
    }

    public function isValid($input): bool
    {
        if (preg_match($this->regExpression, $input) === 1) {

            return true;
        }

        $this->errors =  [ $this->msg ];

        return false;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
