<?php
namespace PrimUtilities;

/*
 * Paginator Service
 * */
class Paginator
{
    protected $currentPage = 0;
    protected $numberOfPages = 1;
    protected $numberOfElements = 1;
    protected $elementsPerPages = 1;
    protected $showPagesNumber = 1;
    protected $backward = false;

    function __construct(int $currentPage, int $numberOfElements, int $elementsPerPages, int $showPagesNumber = 3, bool $backward = false)
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
        $this->backward = $backward;
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
        $result = ($this->currentPage - 1) * $this->elementsPerPages;

        if($this->backward) {
            $result = $this->numberOfElements - ($result + $this->elementsPerPages);
        }

        return $result;
    }

    function getLast() : int
    {
        $result = ($this->currentPage - 1) * $this->elementsPerPages;

        if($this->backward) {
            $result = $this->numberOfElements - $result;
        } else {
            $result += $this->elementsPerPages;
        }

        return $result;
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