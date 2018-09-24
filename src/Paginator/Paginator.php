<?php

namespace Paginator;

class Paginator
{
    const DEFAULT_PAGE_NAME = 'page';
    const DEFAULT_PER_PAGE = 10;
    const DEFAULT_ON_EACH_SIDE = 3;

    private $totalItems;
    private $perPage;
    private $pageName;
    private $url;
    private $onEachSide;

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
    public function setTotalItems($totalItems)
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
    public function setPerPage($perPage)
    {
        $this->perPage = (int) $perPage;

        // after changing perPage, the current page is changed
        $this->updateCurrentPage();
    }

    /**
     * @throws PaginatorException
     */
    private function updateCurrentPage()
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
    public function setCurrentPage($pageNumber)
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
     * @return bool|Page
     * @throws PaginatorException
     */
    public function getNextPage()
    {
        if ($this->isOnLastPage() === true) {
            return false;
        }

        return $this->createPageObject($this->getCurrentPage()->getNumber() + 1);
    }

    /**
     * @return bool|Page
     * @throws PaginatorException
     */
    public function getPreviousPage()
    {
        if ($this->isOnFirstPage() === true) {
            return false;
        }

        return $this->createPageObject($this->getCurrentPage()->getNumber() - 1);
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
    public function isOnFirstPage(): bool
    {
        if ($this->getCurrentPage() instanceof Page && $this->getCurrentPage()->getNumber() === 1) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isOnLastPage(): bool
    {
        if ($this->getCurrentPage() instanceof Page &&
            $this->getCurrentPage()->getNumber() === $this->getNumberOfPages()
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param $currentUrl
     *
     * @return bool|string
     * @throws PaginatorException
     */
    public function getNextPageUrl($currentUrl)
    {
        $nextPage = $this->getNextPage();

        if (!$nextPage instanceof Page) {
            return false;
        }

        return $this->appendQueryStringToURL($currentUrl, [$this->getPageName() => $nextPage->getNumber()]);
    }

    /**
     * @param $currentUrl
     *
     * @return bool|string
     * @throws PaginatorException
     */
    public function getPreviousPageUrl($currentUrl)
    {
        $previousPage = $this->getPreviousPage();

        if (!$previousPage instanceof Page) {
            return false;
        }

        return $this->appendQueryStringToURL($currentUrl, [$this->getPageName() => $previousPage->getNumber()]);
    }

    /**
     * @param string $url
     * @param $query string|array
     * @return string
     */
    private function appendQueryStringToURL(string $url, array $query): string
    {
        $parsedUrl = parse_url($url);
        if (empty($parsedUrl['path'])) {
            $url .= '/';
        }

        $queryString = http_build_query($query);

        // check if there is already any query string in the URL
        if (empty($parsedUrl['query'])) {
            // remove duplications
            parse_str($queryString, $queryStringArray);
            $url .= '?' . http_build_query($queryStringArray);
        } else {
            $queryString = $parsedUrl['query'] . '&' . $queryString;

            // remove duplications
            parse_str($queryString, $queryStringArray);

            // place the updated query in the original query position
            $url = substr_replace(
                $url,
                http_build_query($queryStringArray),
                strpos($url, $parsedUrl['query']),
                strlen($parsedUrl['query'])
            );
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getPageName(): string
    {
        if (empty($this->pageName)) {
            return self::DEFAULT_PAGE_NAME;
        }

        return $this->pageName;
    }

    /**
     * @param string $pageName
     */
    public function setPageName(string $pageName): void
    {
        $this->pageName = $pageName;
    }

    /**
     * @return array
     * @throws PaginatorException
     */
    public function getPages()
    {
        $pages = [];
        for ($i = 1; $i <= $this->getNumberOfPages(); $i++) {
            $pages[$i] = $this->createPageObject($i);
        }

        return $pages;
    }

    /**
     * @param $number
     *
     * @return Page
     * @throws PaginatorException
     */
    private function createPageObject($number)
    {
        $page = new Page($number);
        $number === 1 ? $page->setIsFirst(true) : $page->setIsFirst(false);
        $number === $this->getNumberOfPages() ? $page->setIsLast(true) : $page->setIsLast(false);

        if (!empty($this->getUrl())) {
            $page->setUrl(
                $this->appendQueryStringToURL($this->getUrl(), [$this->getPageName() => $number])
            );
        }

        $onSides = $this->getOnEachSide();
        if ($number > $onSides && $number <= $this->getNumberOfPages() - $onSides) {
            $page->setIsHidden(true);
        } else {
            $page->setIsHidden(false);
        }

        return $page;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getOnEachSide()
    {
        if (!isset($this->onEachSide)) {
            return self::DEFAULT_ON_EACH_SIDE;
        }

        return $this->onEachSide;
    }

    /**
     * @param int $onEachSide
     */
    public function setOnEachSide($onEachSide): void
    {
        $this->onEachSide = $onEachSide;
    }
}
