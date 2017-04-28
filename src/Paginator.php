<?php
namespace PrimUtilities;

class Paginator
{
    public $currentPage = 0;
    public $numberOfPages = 1;
    public $numberOfElements = 1;
    public $elementsPerPages = 1;
    public $showPagesNumber = 1;

    function __construct(int $currentPage, int $numberOfElements, int $elementsPerPages, int $showPagesNumber = 3) : void
    {
        $this->numberOfElements = $numberOfElements;
        $this->elementsPerPages = $elementsPerPages;
        $this->showPagesNumber = $showPagesNumber;

        $this->numberOfPages = ceil($numberOfElements / $elementsPerPages);

        $currentPage = intval($currentPage);
        if ($currentPage == 0) {
            $currentPage = 1;
        }

        // Check currentPage
        if ($currentPage < 1) {
            $this->numberOfPages = 1;
        }
        if ($currentPage > $this->numberOfPages && $this->numberOfPages != 0) {
            $currentPage = $this->numberOfPages;
        }

        $this->currentPage = $currentPage;
    }

    function getNumberPages() : int
    {
        return $this->numberOfPages;
    }

    function getPage() : int
    {
        return $this->currentPage;
    }

    function getFirstPageElement() : int
    {
        return ($this->currentPage - 1) * $this->elementsPerPages;
    }

    function getLast() : int
    {
        return (($this->currentPage - 1) * $this->elementsPerPages) + $this->elementsPerPages;
    }

    function getElementsPerPages() : int
    {
        return $this->elementsPerPages;
    }

    function showPages() : string
    {
        $output = '';
        for($current = ($this->currentPage - 3), $stop = ($this->currentPage + $this->showPagesNumber); $current < $stop; ++$current)
        {
            if($current < 1 || $current > $this->numberOfPages) continue;
            else if($current != $this->currentPage) $output .= '<a href="'.$current.'" class="pageNumberLink">'.$current.'</a> ';
            else $output .= '<span class="pageNumber">'.$current.'</span> ';
        }

        return $output;
    }
}