<?php

namespace Paginator;

abstract class AbstractPaginator implements PaginatorInterface
{
    const DEFAULT_PER_PAGE = 10;

    /**
     * @var int $totalItems
     */
    private $totalItems;

    /**
     * @var int $perPage
     */
    private $perPage;

    /**
     * @var Page $currentPage
     */
    private $currentPage;

    /**
     * Paginator constructor.
     *
     * @param     $totalItems
     * @param int $perPage
     * @param int $currentPageNumber
     *
     * @throws PaginatorException
     */
    public function __construct(
        $totalItems,
        $perPage = self::DEFAULT_PER_PAGE,
        $currentPageNumber = 1
    ) {
        $this->totalItems = (int) $totalItems;
        $this->perPage = (int) $perPage;

        $this->setCurrentPage($currentPageNumber);
    }

    abstract protected function createPageObject($pageNumber);

    /**
     * @return int
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * @param $totalItems
     *
     * @throws PaginatorException
     */
    public function setTotalItems($totalItems): void
    {
        $this->totalItems = (int) $totalItems;

        // after changing totalItems, the current page is changed
        $this->updateCurrentPage();
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @param $perPage
     *
     * @throws PaginatorException
     */
    public function setPerPage($perPage): void
    {
        $this->perPage = (int) $perPage;

        // after changing perPage, the current page is changed
        $this->updateCurrentPage();
    }

    /**
     * @throws PaginatorException
     */
    private function updateCurrentPage(): void
    {
        if ($this->getCurrentPage() instanceof Page) {
            $this->setCurrentPage($this->getCurrentPage()->getNumber());
        } else {
            $this->setCurrentPage(1);
        }
    }

    /**
     * @return Page
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param $pageNumber
     *
     * @throws PaginatorException
     */
    public function setCurrentPage($pageNumber): void
    {
        $pageNumber = (int) $pageNumber;
        if ($this->getNumberOfPages() > 0 &&
            $this->getPerPage() > 0 &&
            $pageNumber > 0 &&
            $pageNumber <= $this->getNumberOfPages()
        ) {
            $this->currentPage = $this->createPageObject($pageNumber);
        } else {
            $this->currentPage = false;
        }
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
}
