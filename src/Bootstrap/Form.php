<?php
namespace sgoranov\Dendroid\Bootstrap;

class Form extends \sgoranov\Dendroid\Component\Form
{
    public function render(\DOMNode $parent): \DOMNode
    {
        /** @var \DOMElement $parent */
        if (!$parent instanceof \DOMElement) {
            throw new \InvalidArgumentException('DOMElement expected');
        }

        $parent = parent::render($parent);

        // add form errors if any
        if (count($this->errors) > 0) {
            $html = "<div class=\"alert alert-danger\" role=\"alert\">";
            foreach ($this->getErrors() as $error) {
                $html .= $error . '<br>';
            }
            $html .= "</div>";

            $newNode = $this->getDOMElementFromString($parent->ownerDocument, $html);
            $parent->parentNode->insertBefore($newNode, $parent);
        }

        return $parent;
    }
}
