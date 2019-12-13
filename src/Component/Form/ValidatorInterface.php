<?php
namespace sgoranov\Dendroid\Component\Form;

interface ValidatorInterface
{
    public function isValid($input): bool;
    public function getErrors(): array;
}