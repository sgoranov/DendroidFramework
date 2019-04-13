<?php
namespace sgoranov\Dendroid\Component\Form;

interface ValidatorInterface
{
    public function isValid(string $input): bool;
    public function getErrors(): array;
}