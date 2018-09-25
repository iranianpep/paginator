<?php

namespace Paginator;

use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    public function testIsFirstPage()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);

        $this->assertTrue($paginator->isOnFirstPage());

        $paginator->setCurrentPage(2);
        $this->assertFalse($paginator->isOnFirstPage());

        $paginator->setCurrentPage(234242525256);
        $this->assertFalse($paginator->isOnFirstPage());
    }

    public function testIsLastPage()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);

        $this->assertFalse($paginator->isOnLastPage());

        // at the moment per page is 10
        $paginator->setTotalItems(9);
        $this->assertTrue($paginator->isOnFirstPage());
        $this->assertTrue($paginator->isOnLastPage());

        // current page number 2, doesn't exist
        $paginator->setCurrentPage(2);
        $this->assertFalse($paginator->isOnFirstPage());
        $this->assertFalse($paginator->isOnLastPage());

        $paginator->setTotalItems(19);
        $paginator->setCurrentPage(2);
        $this->assertFalse($paginator->isOnFirstPage());
        $this->assertTrue($paginator->isOnLastPage());

        $paginator->setPerPage(5);
        $this->assertFalse($paginator->isOnFirstPage());
        $this->assertFalse($paginator->isOnLastPage());

        // still should be on the second page
        $this->assertEquals(2, $paginator->getCurrentPage()->getNumber());
    }

    public function testGetNextPage()
    {
        $totalItems = 3;
        $paginator = new Paginator($totalItems);

        $this->assertFalse($paginator->getNextPage());

        $paginator->setPerPage(1);
        $this->assertEquals(2, $paginator->getNextPage()->getNumber());

        $paginator->setCurrentPage(2);
        $this->assertEquals(3, $paginator->getNextPage()->getNumber());

        $paginator->setCurrentPage(3);
        $this->assertEquals(false, $paginator->getNextPage());

        $paginator->setTotalItems(4);
        $this->assertEquals(4, $paginator->getNextPage()->getNumber());
    }

    public function testGetPreviousPage()
    {
        $totalItems = 3;
        $paginator = new Paginator($totalItems);

        $this->assertFalse($paginator->getPreviousPage());

        $paginator->setPerPage(1);
        $this->assertFalse($paginator->getPreviousPage());

        $paginator->setCurrentPage(2);
        $this->assertEquals(1, $paginator->getPreviousPage()->getNumber());
    }

    public function testGetNextPageUrl()
    {
        $totalItems = 3;
        $paginator = new Paginator($totalItems, 1);

        $this->assertEquals('/product/category?page=2', $paginator->getNextPageUrl('/product/category'));

        $this->assertEquals('/?page=2', $paginator->getNextPageUrl(''));

        $paginator->setCurrentPage(2);
        $this->assertEquals('/product/category?page=3', $paginator->getNextPageUrl('/product/category'));

        // the old query string should be updated with the new one
        $this->assertEquals('/product/category?page=3', $paginator->getNextPageUrl('/product/category?page=1'));

        $this->assertEquals(
            '/product/category?page=3&sortby=date&sortdir=asc',
            $paginator->getNextPageUrl('/product/category?page=1&sortby=date&sortdir=asc')
        );

        $this->assertEquals(
            '/product/category?sortby=date&sortdir=asc&page=3',
            $paginator->getNextPageUrl('/product/category?sortby=date&sortdir=asc&page=1')
        );

        $this->assertEquals(
            'https://example.com/product/category?page=3&sortby=date&sortdir=asc',
            $paginator->getNextPageUrl('https://example.com/product/category?page=1&sortby=date&sortdir=asc')
        );

        $paginator->setPageName('p');
        $this->assertEquals('/product/category?p=3', $paginator->getNextPageUrl('/product/category'));

        $paginator->setCurrentPage(3);
        $this->assertEquals(false, $paginator->getNextPageUrl('/product/category'));
    }

    public function testGetPreviousPageUrl()
    {
        $totalItems = 3;
        $paginator = new Paginator($totalItems, 1);

        $this->assertEquals(false, $paginator->getPreviousPageUrl('/product/category'));

        $paginator->setCurrentPage(2);
        $this->assertEquals('/product/category?page=1', $paginator->getPreviousPageUrl('/product/category'));

        // the old query string should be updated with the new one
        $this->assertEquals('/product/category?page=1', $paginator->getPreviousPageUrl('/product/category?page=2'));

        $this->assertEquals(
            '/product/category?page=1&sortby=date&sortdir=asc',
            $paginator->getPreviousPageUrl('/product/category?page=3&sortby=date&sortdir=asc')
        );

        $this->assertEquals(
            '/product/category?sortby=date&sortdir=asc&page=1',
            $paginator->getPreviousPageUrl('/product/category?sortby=date&sortdir=asc&page=3')
        );

        $this->assertEquals(
            'https://example.com/product/category?page=1&sortby=date&sortdir=asc',
            $paginator->getPreviousPageUrl('https://example.com/product/category?page=3&sortby=date&sortdir=asc')
        );

        $paginator->setPageName('p');
        $this->assertEquals('/product/category?p=1', $paginator->getPreviousPageUrl('/product/category'));

        $paginator->setCurrentPage(1);
        $this->assertEquals(false, $paginator->getPreviousPageUrl('/product/category'));
    }

    public function testGetPageName()
    {
        $totalItems = 3;
        $paginator = new Paginator($totalItems);

        // page name is not set, so default gets returned
        $this->assertEquals(Paginator::DEFAULT_PAGE_NAME, $paginator->getPageName());

        $paginator->setPageName('p');
        $this->assertEquals('p', $paginator->getPageName());

        $paginator->setPageName('');

        // page name is set to none, default gets returned
        $this->assertEquals(Paginator::DEFAULT_PAGE_NAME, $paginator->getPageName());
    }

    public function testGetPages()
    {
        $totalItems = 7;
        $paginator = new Paginator($totalItems, 1);
        $paginator->setUrl('https://www.example.com');

        $pages = $paginator->getPages();
        $this->assertEquals(7, count($pages));
        $this->assertEquals(true, $pages[1]->isFirst());
        $this->assertEquals(false, $pages[1]->isLast());
        $this->assertEquals('https://www.example.com/?page=1', $pages[1]->getUrl());
        $this->assertEquals(false, $pages[1]->isHidden());

        $this->assertEquals(false, $pages[4]->isFirst());
        $this->assertEquals(false, $pages[4]->isLast());
        $this->assertEquals('https://www.example.com/?page=4', $pages[4]->getUrl());
        $this->assertEquals(true, $pages[4]->isHidden());

        $this->assertEquals(false, $pages[7]->isFirst());
        $this->assertEquals(true, $pages[7]->isLast());
        $this->assertEquals('https://www.example.com/?page=7', $pages[7]->getUrl());
        $this->assertEquals(false, $pages[7]->isHidden());

        $totalItems = 1;
        $paginator = new Paginator($totalItems);

        $pages = $paginator->getPages();
        $this->assertEquals(1, count($pages));
        $this->assertEquals(true, $pages[1]->isFirst());
        $this->assertEquals(true, $pages[1]->isLast());
        $this->assertEmpty($pages[1]->getUrl());
        $this->assertEquals(false, $pages[1]->isHidden());

        $totalItems = 3;
        $paginator = new Paginator($totalItems);
        $paginator->setPerPage(1);
        $paginator->setOnEachSide(1);

        $pages = $paginator->getPages();
        $this->assertEquals(3, count($pages));
        $this->assertEquals(true, $pages[1]->isFirst());
        $this->assertEquals(false, $pages[1]->isLast());
        $this->assertEmpty($pages[1]->getUrl());
        $this->assertEquals(false, $pages[1]->isHidden());

        $this->assertEquals(false, $pages[2]->isFirst());
        $this->assertEquals(false, $pages[2]->isLast());
        $this->assertEmpty($pages[2]->getUrl());
        $this->assertEquals(true, $pages[2]->isHidden());

        $this->assertEquals(false, $pages[3]->isFirst());
        $this->assertEquals(true, $pages[3]->isLast());
        $this->assertEmpty($pages[3]->getUrl());
        $this->assertEquals(false, $pages[3]->isHidden());
    }
}
