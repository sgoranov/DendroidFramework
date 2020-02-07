<?php
namespace sgoranov\Dendroid\Component\Form;

class Input extends Element
{
    protected $type = 'text';
    
    public function setMin(\DateTime $date = null)
    {
        if ($this->type !== 'date') {
            throw new \Exception(sprintf('Unsuported operation for type %s', $this->type));
        }

        if ($date) {
            $this->attributes['min'] = $date->format('Y-m-d');
        } else {
            unset($this->attributes['min']);
        }
    }

    public function setMax(\DateTime $date = null)
    {
        if ($this->type !== 'date') {
            throw new \Exception(sprintf('Unsuported operation for type %s', $this->type));
        }

        if ($date) {
            $this->attributes['max'] = $date->format('Y-m-d');
        } else {
            unset($this->attributes['max']);
        }
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

    public function setType($type = 'text')
    {
        $this->type = $type;
    }

    public function render(\DOMNode $node): \DOMNode
    {
        if (!$node instanceof \DOMElement) {
            throw new \InvalidArgumentException('DOMElement expected');
        }

        // overwrite the name of the form field
        $node->setAttribute('name', $this->getNameDefinition());

        // set all additional attributes
        foreach ($this->attributes as $key => $value) {
            $node->setAttribute($key, $value);
        }

        $node->setAttribute('value', $this->getDataToRender());

        // set type of the input, type="text" by default
        $node->setAttribute('type', $this->type);

        return $node;
    }
}