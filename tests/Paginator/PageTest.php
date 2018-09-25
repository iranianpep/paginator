<?php

namespace Paginator;

use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    /**
     * @throws PaginatorException
     */
    public function testGetNumber()
    {
        $page = new Page(1);
        $this->assertEquals(1, $page->getNumber());

        try {
            $page->setNumber(-1);
        } catch (\Exception $e) {
            $this->assertEquals('Invalid page number', $e->getMessage());
        }
    }

    /**
     * @throws PaginatorException
     */
    public function testIsFirst()
    {
        $page = new Page(1);
        $this->assertFalse($page->isFirst());

        $page->setIsFirst(true);
        $this->assertTrue($page->isFirst());

        $page->setIsFirst(false);
        $this->assertFalse($page->isFirst());
    }

    /**
     * @throws PaginatorException
     */
    public function testIsLast()
    {
        $page = new Page(1);
        $this->assertFalse($page->isLast());

        $page->setIsLast(true);
        $this->assertTrue($page->isLast());

        $page->setIsLast(false);
        $this->assertFalse($page->isLast());
    }

    /**
     * @throws PaginatorException
     */
    public function testIsHidden()
    {
        $page = new Page(1);
        $this->assertFalse($page->isHidden());

        $page->setIsHidden(true);
        $this->assertTrue($page->isHidden());

        $page->setIsHidden(false);
        $this->assertFalse($page->isHidden());
    }

    /**
     * @throws PaginatorException
     */
    public function testGetUrl()
    {
        $page = new Page(1);
        $this->assertEmpty($page->getUrl());

        $page->setUrl('https://example.com');
        $this->assertEquals('https://example.com', $page->getUrl());
    }

    /**
     * @throws PaginatorException
     */
    public function testIsCurrent()
    {
        $page = new Page(1);
        $this->assertFalse($page->isCurrent());

        $page->setIsCurrent(true);
        $this->assertTrue($page->isCurrent());

        $page->setIsCurrent(false);
        $this->assertFalse($page->isCurrent());
    }
}
