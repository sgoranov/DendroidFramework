<?php
namespace sgoranov\Dendroid\Bootstrap\Component;

use sgoranov\Dendroid\Component;

class Pagination extends Component
{
    const PAGE_RANGE = 3;

    private $url;
    private $totalNumberOfItems;
    private $totalNumberOfPages;
    private $currentPage;
    private $itemsPerPage;

    private $previousLabel = 'Previous';
    private $nextLabel = 'Next';

    public function __construct(string $url, int $currentPage, int $totalNumberOfItems, int $itemsPerPage = 10)
    {
        $this->url = $url;
        if (strstr($this->url, '{PAGE}') === false) {
            throw new \InvalidArgumentException('The url parameter must contains the {PAGE} placeholder');
        }

        $this->totalNumberOfItems = $totalNumberOfItems;
        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
        $this->totalNumberOfPages = (int)ceil($this->totalNumberOfItems / $this->itemsPerPage);
    }

    public function setPaginationLabels(string $previous, string $next)
    {
        $this->previousLabel = $previous;
        $this->nextLabel = $next;
    }

    public function getOffset()
    {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }

    public function render(\DOMNode $node): \DOMNode
    {
        $disabledPrevious = '';
        if ($this->currentPage === 1) {
            $disabledPrevious = 'disabled';
        }

        $html = "
            <nav aria-label=\"...\">
                <ul class=\"pagination\">
                    <li class=\"page-item $disabledPrevious \">
                        <a class=\"page-link\" href=\"" . str_replace('{PAGE}', $this->currentPage - 1, $this->url) . "\" tabindex=\"-1\">{$this->previousLabel}</a>
                    </li>      
        ";

        for ($page = 1; $page <= $this->totalNumberOfPages; $page++) {

            if ($page + self::PAGE_RANGE < $this->currentPage || $this->currentPage + self::PAGE_RANGE < $page) {
                continue;
            }

            $isActive = '';
            if ($this->currentPage === $page) {
                $isActive = 'active';
            }

            $html .= "
                <li class=\"page-item $isActive\">
                    <a class=\"page-link\" href=\"" . str_replace('{PAGE}', $page, $this->url) . "\">$page</a>
                </li>
            ";
        }

        $nextDisabled = '';
        if ($this->currentPage === $this->totalNumberOfPages) {
            $nextDisabled = 'disabled';
        }

        $html .= "
                    <li class=\"page-item $nextDisabled\">
                        <a class=\"page-link\" href=\"" . str_replace('{PAGE}', $this->currentPage + 1, $this->url) . "\">{$this->nextLabel}</a>
                    </li>
                </ul>
            </nav>
        ";

        $newNode = $this->getDOMElementFromString($node->ownerDocument, $html);
        $node->appendChild($newNode);

        return $node;
    }
}
