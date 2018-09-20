<?php

namespace Paginator;

class Paginator
{
    private $totalItems;
    private $perPage;
    private $currentPage;

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
        } elseif ($currentPage >= $this->getNumberOfPages()) {
            $this->currentPage = $this->getNumberOfPages() - 1;
        } else {
            $this->currentPage = $currentPage;
        }
    }

    public function getNumberOfPages()
    {
        $totalRecords = $this->getTotalItems();
        $pageSize = $this->getPerPage();

        if ($totalRecords === 0 || $pageSize === 0) {
            return 0;
        }

        if ($totalRecords < $pageSize) {
            return 1;
        }

        if ($totalRecords % $pageSize === 0) {
            return $totalRecords/$pageSize;
        } else {
            return ceil($totalRecords/$pageSize);
        }
    }
}
