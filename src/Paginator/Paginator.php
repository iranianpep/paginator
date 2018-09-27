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

    public function __construct(
        $totalItems,
        $perPage = self::DEFAULT_PER_PAGE,
        $currentPageNumber = 1,
        $url = ''
    ) {
        parent::__construct($totalItems, $perPage, $currentPageNumber);
        $this->setUrl($url);
    }

    /**
     * @throws PaginatorException
     *
     * @return bool|Page
     */
    public function getNextPage()
    {
        $currentPage = $this->getCurrentPage();
        if ($this->isOnLastPage() === true || !$currentPage instanceof Page) {
            return false;
        }

        return $this->createPageObject($currentPage->getNumber() + 1);
    }

    /**
     * @throws PaginatorException
     *
     * @return bool|Page
     */
    public function getPreviousPage()
    {
        $currentPage = $this->getCurrentPage();
        if ($this->isOnFirstPage() === true || !$currentPage instanceof Page) {
            return false;
        }

        return $this->createPageObject($currentPage->getNumber() - 1);
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
            $this->getCurrentPage()->getNumber() === $this->calculateNumberOfPages()
        ) {
            return true;
        }

        return false;
    }

    /**
     * @throws PaginatorException
     *
     * @return bool|string
     */
    public function getNextPageUrl()
    {
        $nextPage = $this->getNextPage();

        if (!$nextPage instanceof Page) {
            return false;
        }

        return $this->appendQueryStringToURL($this->getUrl(), [$this->getPageName() => $nextPage->getNumber()]);
    }

    /**
     * @throws PaginatorException
     *
     * @return bool|string
     */
    public function getPreviousPageUrl()
    {
        $previousPage = $this->getPreviousPage();

        if (!$previousPage instanceof Page) {
            return false;
        }

        return $this->appendQueryStringToURL($this->getUrl(), [$this->getPageName() => $previousPage->getNumber()]);
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

            return $url.'?'.http_build_query($queryStringArray);
        }

        $queryString = $parsedUrl[$queryKey].'&'.$queryString;

        // remove duplications
        parse_str($queryString, $queryStringArray);

        // place the updated query in the original query position
        return substr_replace(
            $url,
            http_build_query($queryStringArray),
            strpos($url, $parsedUrl[$queryKey]),
            strlen($parsedUrl[$queryKey])
        );
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
        $number === $this->calculateNumberOfPages() ? $page->setIsLast(true) : $page->setIsLast(false);
        $this->getCurrentPage() instanceof Page && $number === $this->getCurrentPage()->getNumber() ?
            $page->setIsCurrent(true) : $page->setIsCurrent(false);

        if (!empty($this->getUrl())) {
            $page->setUrl(
                $this->appendQueryStringToURL($this->getUrl(), [$this->getPageName() => $number])
            );
        }

        $this->isPageWithinHiddenRange($page->getNumber()) ? $page->setIsHidden(true) : $page->setIsHidden(false);

        return $page;
    }

    private function isSliderCloseToBeginning()
    {
        if ($this->getCurrentPage() instanceof Page &&
            $this->getCurrentPage()->getNumber() <= (2 * $this->getOnEachSide())) {
            return true;
        }

        return false;
    }

    private function isSliderCloseToEnding()
    {
        if ($this->getCurrentPage() instanceof Page &&
            $this->getCurrentPage()->getNumber() > ($this->calculateNumberOfPages() - (2 * $this->getOnEachSide()))) {
            return true;
        }

        return false;
    }

    private function isPageWithinHiddenRange($number)
    {
        $hiddenRanges = $this->getHiddenRanges();
        foreach ($hiddenRanges as $hiddenRange) {
            if ($number > $hiddenRange['start'] && $number < $hiddenRange['finish']) {
                return true;
            }
        }

        return false;
    }

    public function getHiddenRanges()
    {
        $onSides = $this->getOnEachSide();

        if (!$this->getCurrentPage() instanceof Page || $this->isSliderCloseToBeginning() === true) {
            return [
                'start' => (2 * $onSides) + 2,
                'finish' => $this->calculateNumberOfPages() - 2
            ];
        } elseif ($this->isSliderCloseToEnding() === true) {
            return [
                'start' => 2,
                'finish' => $this->calculateNumberOfPages() - ((2 * $onSides) + 2)
            ];
        }

        return [
            [
                'start' => 2,
                'finish' => $this->getCurrentPage()->getNumber() - $onSides
            ],
            [
                'start' => $this->getCurrentPage()->getNumber() + $onSides,
                'finish' => $this->calculateNumberOfPages() - 2
            ]
        ];
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

    /**
     * @param $pageNumber
     *
     * @return int
     */
    public function calculateDatabaseOffset($pageNumber): int
    {
        $pageNumber = (int) $pageNumber;

        if ($pageNumber > 0) {
            return ($pageNumber - 1) * $this->getPerPage();
        }

        return 0;
    }
}
