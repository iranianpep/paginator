<?php

namespace Paginator;

class Paginator
{
    const DEFAULT_PAGE_NAME = 'page';
    const DEFAULT_PER_PAGE = 10;
    const DEFAULT_CURRENT_PAGE = 1;

    private $totalItems;
    private $perPage;
    private $currentPage;
    private $pageName;

    /**
     * Paginator constructor.
     *
     * @param     $totalItems
     * @param int $perPage
     * @param int $currentPage
     */
    public function __construct(
        $totalItems,
        $perPage = self::DEFAULT_PER_PAGE,
        $currentPage = self::DEFAULT_CURRENT_PAGE
    ) {
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

    /**
     * @param $currentUrl
     *
     * @return bool|string
     */
    public function getNextPageUrl($currentUrl)
    {
        $nextPage = $this->getNextPage();

        if (empty($nextPage)) {
            return false;
        }

        return $this->appendQueryStringToURL($currentUrl, [$this->getPageName() => $nextPage]);
    }

    /**
     * @param $currentUrl
     *
     * @return bool|string
     */
    public function getPreviousPageUrl($currentUrl)
    {
        $previousPage = $this->getPreviousPage();

        if (empty($previousPage)) {
            return false;
        }

        return $this->appendQueryStringToURL($currentUrl, [$this->getPageName() => $previousPage]);
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
}
