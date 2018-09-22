<?php

namespace Paginator;

class Paginator
{
    private $totalItems;
    private $perPage;
    private $currentPage;

    /**
     * Paginator constructor.
     *
     * @param     $totalItems
     * @param int $perPage
     * @param int $currentPage
     */
    public function __construct($totalItems, $perPage = 10, $currentPage = 1)
    {
        $this->setTotalItems($totalItems);
        $this->setPerPage($perPage);
        $this->setCurrentPage($currentPage);
    }

    /**
     * @return int
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * @param int $totalItems
     */
    public function setTotalItems($totalItems)
    {
        $this->totalItems = (int) $totalItems;

        // after changing totalItems, the current page is changed
        $this->setCurrentPage($this->getCurrentPage());
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage($perPage)
    {
        $this->perPage = (int) $perPage;

        // after changing perPage, the current page is changed
        $this->setCurrentPage($this->getCurrentPage());
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $currentPage = (int) $currentPage;
        if ($currentPage < 1) {
            $this->currentPage = 1;
        } elseif ($currentPage > $this->getNumberOfPages()) {
            $this->currentPage = $this->getNumberOfPages();
        } else {
            $this->currentPage = $currentPage;
        }
    }

    /**
     * @return bool|int
     */
    public function getNextPage()
    {
        if ($this->isLastPage() === true) {
            return false;
        }

        return $this->getCurrentPage() + 1;
    }

    /**
     * @return bool|int
     */
    public function getPreviousPage()
    {
        if ($this->isFirstPage() === true) {
            return false;
        }

        return $this->getCurrentPage() - 1;
    }

    /**
     * @return int
     */
    public function getNumberOfPages(): int
    {
        $totalRecords = $this->getTotalItems();
        $pageSize = $this->getPerPage();

        if ($totalRecords === 0 || $pageSize === 0) {
            return 0;
        }

        if ($totalRecords < $pageSize) {
            $numberOfPages = 1;
        } elseif ($totalRecords % $pageSize === 0) {
            $numberOfPages = $totalRecords/$pageSize;
        } else {
            $numberOfPages = ceil($totalRecords/$pageSize);
        }

        return $numberOfPages;
    }

    /**
     * @return bool
     */
    public function hasPages(): bool
    {
        if ($this->getNumberOfPages() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isFirstPage(): bool
    {
        if ($this->getCurrentPage() === 1) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isLastPage(): bool
    {
        if ($this->getCurrentPage() === $this->getNumberOfPages()) {
            return true;
        }

        return false;
    }
}
