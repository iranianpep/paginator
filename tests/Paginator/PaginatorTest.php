<?php

namespace Paginator;

use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    public function testGetTotalItems()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);
        $this->assertEquals($totalItems, $paginator->getTotalItems());

        $totalItems = 0;
        $paginator = new Paginator($totalItems);
        $this->assertEquals($totalItems, $paginator->getTotalItems());

        $totalItems = 'invalid number';
        $paginator = new Paginator($totalItems);
        $this->assertEquals(0, $paginator->getTotalItems());

        $paginator->setTotalItems(25);
        $this->assertEquals(25, $paginator->getTotalItems());
    }

    public function testGetPerPage()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);
        $this->assertEquals(10, $paginator->getPerPage());

        $paginator = new Paginator($totalItems, 5);
        $this->assertEquals(5, $paginator->getPerPage());

        $paginator = new Paginator($totalItems, 0);
        $this->assertEquals(0, $paginator->getPerPage());

        $paginator->setPerPage(10);
        $this->assertEquals(10, $paginator->getPerPage());
    }

    public function testGetCurrentPage()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);

        // if current page is not set, 1 is considered
        $this->assertEquals(1, $paginator->getCurrentPage());

        $paginator = new Paginator($totalItems, 10, 4);
        $this->assertEquals(4, $paginator->getCurrentPage());

        $paginator = new Paginator($totalItems, 10, 5);
        $this->assertEquals(5, $paginator->getCurrentPage());

        $paginator = new Paginator($totalItems, 10, 6);

        // because maximum page is 5
        $this->assertEquals(5, $paginator->getCurrentPage());

        // because minimum page is 1
        $paginator = new Paginator($totalItems, 10, 0);
        $this->assertEquals(1, $paginator->getCurrentPage());

        $paginator->setCurrentPage(4);
        $this->assertEquals(4, $paginator->getCurrentPage());

        $paginator->setPerPage(50);
        $this->assertEquals(1, $paginator->getCurrentPage());

        $paginator->setTotalItems(5);
        $paginator->setPerPage(1);
        $this->assertEquals(1, $paginator->getCurrentPage());

        $paginator->setCurrentPage(2);
        $paginator->setPerPage(2);

        // it still should be in second page
        $this->assertEquals(2, $paginator->getCurrentPage());

        $paginator->setPerPage(3);
        $this->assertEquals(2, $paginator->getCurrentPage());

        $paginator->setPerPage(5);
        $this->assertEquals(1, $paginator->getCurrentPage());

        // move the current page to the last page
        $paginator->setPerPage(1);
        $paginator->setCurrentPage(5);

        // if per page is changed, the current page should be still the last page
        $paginator->setPerPage(2);
        $this->assertEquals(3, $paginator->getCurrentPage());

        // total number of items is changed from 5 to 3, which reduces the number of pages to 2 (per page is 2)
        $paginator->setTotalItems(3);
        $this->assertEquals(2, $paginator->getCurrentPage());

        $paginator->setTotalItems(1);
        $this->assertEquals(1, $paginator->getCurrentPage());

        $paginator->setTotalItems(9);
        $this->assertEquals(1, $paginator->getCurrentPage());
    }

    public function testGetNumberOfPages()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);
        $this->assertEquals(5, $paginator->getNumberOfPages());

        $paginator = new Paginator($totalItems, 49);
        $this->assertEquals(2, $paginator->getNumberOfPages());

        $paginator = new Paginator($totalItems, 50);
        $this->assertEquals(1, $paginator->getNumberOfPages());

        $paginator = new Paginator($totalItems, 51);
        $this->assertEquals(1, $paginator->getNumberOfPages());

        $paginator = new Paginator(0);
        $this->assertEquals(0, $paginator->getNumberOfPages());

        $paginator = new Paginator($totalItems, 0);
        $this->assertEquals(0, $paginator->getNumberOfPages());

        $paginator->setPerPage(5);
        $this->assertEquals(10, $paginator->getNumberOfPages());

        $paginator->setTotalItems(500);
        $this->assertEquals(100, $paginator->getNumberOfPages());
    }

    public function testHasPages()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);

        $this->assertTrue($paginator->hasPages());

        $paginator->setTotalItems(1);
        $this->assertTrue($paginator->hasPages());

        $paginator->setTotalItems(0);
        $this->assertFalse($paginator->hasPages());

        $paginator->setTotalItems(50);
        $paginator->setPerPage(0);
        $this->assertFalse($paginator->hasPages());

        $paginator->setPerPage(500);
        $paginator->setCurrentPage(1000);
        $this->assertTrue($paginator->hasPages());
    }

    public function testIsFirstPage()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);

        $this->assertTrue($paginator->isFirstPage());

        $paginator->setCurrentPage(2);
        $this->assertFalse($paginator->isFirstPage());

        $paginator->setCurrentPage(234242525256);
        $this->assertFalse($paginator->isFirstPage());
    }

    public function testIsLastPage()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);

        $this->assertFalse($paginator->isLastPage());

        // at the moment per page is 10
        $paginator->setTotalItems(9);
        $this->assertTrue($paginator->isFirstPage());
        $this->assertTrue($paginator->isLastPage());

        $paginator->setCurrentPage(2);
        $this->assertTrue($paginator->isFirstPage());
        $this->assertTrue($paginator->isLastPage());

        $paginator->setTotalItems(19);
        $paginator->setCurrentPage(2);
        $this->assertFalse($paginator->isFirstPage());
        $this->assertTrue($paginator->isLastPage());

        $paginator->setPerPage(5);
        $this->assertFalse($paginator->isFirstPage());
        $this->assertFalse($paginator->isLastPage());

        // still should be on the second page
        $this->assertEquals(2, $paginator->getCurrentPage());
    }

    public function testGetNextPage()
    {
        $totalItems = 3;
        $paginator = new Paginator($totalItems);

        $this->assertFalse($paginator->getNextPage());

        $paginator->setPerPage(1);
        $this->assertEquals(2, $paginator->getNextPage());

        $paginator->setCurrentPage(2);
        $this->assertEquals(3, $paginator->getNextPage());

        $paginator->setCurrentPage(3);
        $this->assertEquals(false, $paginator->getNextPage());

        $paginator->setTotalItems(4);
        $this->assertEquals(4, $paginator->getNextPage());
    }

    public function testGetPreviousPage()
    {
        $totalItems = 3;
        $paginator = new Paginator($totalItems);

        $this->assertFalse($paginator->getPreviousPage());

        $paginator->setPerPage(1);
        $this->assertFalse($paginator->getPreviousPage());

        $paginator->setCurrentPage(2);
        $this->assertEquals(1, $paginator->getPreviousPage());
    }

    public function testGetNextPageUrl()
    {
        $totalItems = 3;
        $paginator = new Paginator($totalItems, 1);

        $this->assertEquals('/product/category?page=2', $paginator->getNextPageUrl('/product/category'));

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

        $paginator->setCurrentPage(1);
        $this->assertEquals(false, $paginator->getPreviousPageUrl('/product/category'));
    }
}
