<?php
namespace sgoranov\Dendroid\Validator;

use sgoranov\Dendroid\Component\Form\ValidatorInterface;

class Chain implements ValidatorInterface
{
    private bool $stopOnFailure;
    private array $validators = [];
    private array $errors = [];

    public function __construct(bool $stopOnFailure = false)
    {
        $this->stopOnFailure = $stopOnFailure;
    }

    public function addValidator(ValidatorInterface $validator)
    {
        $this->validators[] = $validator;
    }

    public function isValid($input): bool
    {
        $result = true;

        /** @var ValidatorInterface $validator */
        foreach ($this->validators as $validator) {

            if (!$validator->isValid($input)) {

                $result = false;
                $this->errors = array_merge($this->errors, $validator->getErrors());

                if ($this->stopOnFailure) {
                    break;
                }
            }
        }

        return $result;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}