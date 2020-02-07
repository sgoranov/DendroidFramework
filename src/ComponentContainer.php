<?php
namespace sgoranov\Dendroid;

/**
 * Class ComponentContainer
 *
 * This component may contains sub components
 */
abstract class ComponentContainer extends Component implements ComponentContainerInterface
{
    protected $template = false;
    protected $components = [];

    private function getInnerHtml(\DOMNode $element)
    {
        $innerHTML = "";
        $children  = $element->childNodes;

        foreach ($children as $child)
        {
            $innerHTML .= $element->ownerDocument->saveHTML($child);
        }

        return $innerHTML;
    }

    public function render(\DOMNode $parent): \DOMNode
    {
        if ($parent instanceof \DOMDocument) {
            $xpath = new \DOMXPath($parent);
        } else if ($parent instanceof \DOMElement) {

            // when an element is passed we are going to load the page template
            // and then parse that template instead working with the element
            $html = $this->getHtml();
            if ($html === false) {
                $html = $this->getInnerHtml($parent);
            }

            if (empty($html)) {
                throw new \Exception('Define a template using setTemplate() or overwrite getHtml or define the html as inner content');
            }

            $dom = $this->getDOMFromString($html);
            $xpath = new \DOMXPath($dom);

        } else {
            throw new \InvalidArgumentException('DOMDocument or DOMElement expected');
        }

        $nodes = $xpath->query('//*[@spf][count(ancestor-or-self::node()[@spf])<2]');

        $spfNodes = [];

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            $reference = $node->getAttribute('spf');

            if (!array_key_exists($reference, $this->components)) {
                throw new \InvalidArgumentException("The node with '$reference' reference has no matching component.");
            }

            /** @var Component $component */
            $component = $this->components[$reference];
            $node->parentNode->replaceChild($component->render($node), $node);

            $spfNodes[] = $reference;
        }

        if ($parent instanceof \DOMElement) {

            // clear the node
            while ($parent->hasChildNodes()) {
                $parent->removeChild($parent->firstChild);
            }

            // append the new content from the template
            foreach ($dom->documentElement->childNodes as $child) {
                $parent->appendChild(
                    $parent->ownerDocument->importNode($child, true)
                );
            }
        }

        return $parent;
    }

    public function escape(string $string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public function getHtml()
    {
        if ($this->template !== false) {
            $path = $this->template;

            try {
                ob_start();
                include $path;
                $content = ob_get_clean();
            } catch (\Throwable $e) {
                ob_end_clean();

                throw $e;
            } catch (\Exception $e) {
                ob_end_clean();

                throw $e;
            }

            return $content;
        }

        return false;
    }

    public function setTemplate($path, bool $isRelative = true)
    {
        if ($isRelative) {
            $reflector = new \ReflectionClass($this);
            $fn = $reflector->getFileName();
            $directory = dirname($fn);

            $path = $directory . DIRECTORY_SEPARATOR . $path;
        }

        $this->template = $path;
    }

    protected function getDOMFromFile($path)
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new \InvalidArgumentException("Path not found: $path");
        }

        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

        return $dom;
    }

    protected function getDOMFromString($content)
    {
        $dom = new \DOMDocument();

        // disable HTML errors/warnings
        $isLoaded = @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

        if (!$isLoaded) {
            throw new \InvalidArgumentException("Unable to load the HTML");
        }

        return $dom;
    }

    public function addComponent(string $ref, ComponentInterface $component)
    {
        $this->components[$ref] = $component;
    }

    public function getComponent($ref): array
    {
        if (!isset($this->components[$ref])) {
            throw new \InvalidArgumentException(sprintf('Component with ref %s is not found', $ref));
        }

        return $this->components[$ref];
    }

    public function getComponents(): array
    {
        return $this->components;
    }
}
