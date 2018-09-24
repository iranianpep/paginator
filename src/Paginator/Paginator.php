<?php

namespace Paginator;

class Paginator extends AbstractPaginator
{
    const DEFAULT_PAGE_NAME = 'page';
    const DEFAULT_ON_EACH_SIDE = 3;

    /**
     * @var string
     */
    private $pageName;

    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $onEachSide;

    /**
     * @throws PaginatorException
     *
     * @return bool|Page
     */
    public function getNextPage()
    {
        if ($this->isOnLastPage() === true) {
            return false;
        }

        return $this->createPageObject($this->getCurrentPage()->getNumber() + 1);
    }

    /**
     * @throws PaginatorException
     *
     * @return bool|Page
     */
    public function getPreviousPage()
    {
        if ($this->isOnFirstPage() === true) {
            return false;
        }

        return $this->createPageObject($this->getCurrentPage()->getNumber() - 1);
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
     * @throws PaginatorException
     *
     * @return bool|string
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
     * @throws PaginatorException
     *
     * @return bool|string
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
     *
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
        $queryKey = 'query';
        if (empty($parsedUrl[$queryKey])) {
            // remove duplications
            parse_str($queryString, $queryStringArray);
            $url .= '?'.http_build_query($queryStringArray);
        } else {
            $queryString = $parsedUrl[$queryKey].'&'.$queryString;

            // remove duplications
            parse_str($queryString, $queryStringArray);

            // place the updated query in the original query position
            $url = substr_replace(
                $url,
                http_build_query($queryStringArray),
                strpos($url, $parsedUrl[$queryKey]),
                strlen($parsedUrl[$queryKey])
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
     * @throws PaginatorException
     *
     * @return array
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
     * @throws PaginatorException
     *
     * @return Page
     */
    protected function createPageObject($number)
    {
        $page = new Page($number);
        $number === 1 ? $page->setIsFirst(true) : $page->setIsFirst(false);
        $number === $this->getNumberOfPages() ? $page->setIsLast(true) : $page->setIsLast(false);

        if (!empty($this->getUrl())) {
            $page->setUrl(
                $this->appendQueryStringToURL($this->getUrl(), [$this->getPageName() => $number])
            );
        }

        $page->setIsHidden(false);
        $onSides = $this->getOnEachSide();
        if ($number > $onSides && $number <= $this->getNumberOfPages() - $onSides) {
            $page->setIsHidden(true);
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
    public function getOnEachSide(): int
    {
        if (!isset($this->onEachSide)) {
            return self::DEFAULT_ON_EACH_SIDE;
        }

        return (int) $this->onEachSide;
    }

    /**
     * @param int $onEachSide
     */
    public function setOnEachSide($onEachSide): void
    {
        $this->onEachSide = $onEachSide;
    }
}
