<?php

namespace Paginator;

class Page
{
    private $number;
    private $isFirst;
    private $isLast;
    private $isHidden;
    private $url;

    /**
     * Page constructor.
     *
     * @param $number
     *
     * @throws PaginatorException
     */
    public function __construct(int $number)
    {
        $this->setNumber($number);
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param $number
     *
     * @throws PaginatorException
     */
    public function setNumber(int $number): void
    {
        if ($this->isPageNumberValid($number) !== true) {
            throw new PaginatorException('Invalid page number');
        }

        $this->number = $number;
    }

    /**
     * @param $number
     *
     * @return bool
     */
    private function isPageNumberValid($number): bool
    {
        if ($number > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isFirst(): bool
    {
        return (bool) $this->isFirst;
    }

    /**
     * @param bool $isFirst
     */
    public function setIsFirst($isFirst): void
    {
        $this->isFirst = $isFirst;
    }

    /**
     * @return bool
     */
    public function isLast(): bool
    {
        return (bool) $this->isLast;
    }

    /**
     * @param bool $isLast
     */
    public function setIsLast($isLast): void
    {
        $this->isLast = $isLast;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return (bool) $this->isHidden;
    }

    /**
     * @param bool $isHidden
     */
    public function setIsHidden($isHidden): void
    {
        $this->isHidden = $isHidden;
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
}
